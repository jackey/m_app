<?php
// require_once "Mage/Customer/controllers/AccountController.php";  
require_once(Mage::getModuleDir('controllers', 'Mage_Customer').'/AccountController.php');
class Mec_Lily_Customer_AccountController extends Mage_Customer_AccountController{

	 public function createPostAction()
	 {
	 
		$verify_code = Mage::getSingleton('core/session')->getVerify();
		$session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
		
		$session->setEscapeMessages(true); // prevent XSS injection in user input
		if ($this->getRequest()->isPost()) {		

			
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);
			
			
			
			$_post = $this->getRequest()->getPost();
			$firstname = $this->getRequest()->getPost('firstname');
			$_post['lastname'] = $firstname;
			$this->getRequest()->setPost($_post); 
			
			
			
            $customerData = $customerForm->extractData($this->getRequest());
		
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);

                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try {
				
				if($verify_code != $this->getRequest()->getPost('verify_code')){
					 $session->addError($this->__('Invalid Verify Code.'));
					 $session->setCustomerFormData($this->getRequest()->getPost());
					 $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
					 return;
					 
				}
				
				
				if(!Mage::helper('lily')->telIsExist($this->getRequest()->getPost('telephone'))){
					 $session->addError($this->__('Telphone Has Already In System.'));
					 $session->setCustomerFormData($this->getRequest()->getPost());
					 $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
					 return;
					
				}
				
				
				
				$_tel_result = Mage::helper('lily')->ErpHasTelephone($this->getRequest()->getPost('telephone'));
				$_tel_result = json_decode($_tel_result);
				$_tel_result = $_tel_result[0];
				if($_tel_result->code == 0){
					if($_tel_result->rows != ""){
						Mage::helper('lily')->AddCustomerFromErp($_tel_result->rows, $this->getRequest()->getPost('telephone'));
						$session->addError($this->__('Telphone Has Already In System.'));
						$session->setCustomerFormData($this->getRequest()->getPost());
						$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
						return;
					}
				}
				
				
				
				
				
				
				
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }
				
                $validationResult = count($errors) == 0;
				
                if (true === $validationResult) {
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        // $this->_redirectSuccess($url);
                        // return;
						$this->_redirect('*/*/edit');
						return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }
		
		$this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
	 }

	
	public function loginPostAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/edit');
            return;
        }
		
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['telphone']) && !empty($login['password'])) {
                try {
					$email = Mage::helper('lily')->getEmailByTelphone($login['telphone']);
					if($email){
						 $session->login($email, $login['password']);
						if ($session->getCustomer()->getIsJustConfirmed()) {
							$this->_welcomeCustomer($session->getCustomer(), true);
						}
					}else{
						$message = Mage::helper('customer')->__('Wrong Telphone');
						$session->addError($message);
						// $this->_loginPostRedirect();
						$url = Mage::getBaseUrl() . '#club';
						$this->_redirectUrl($url);
						return;
					}
                   
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['telphone']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
			    $this->_redirect('*/*/edit');
				return;
            } else {
                $session->addError($this->__('Telphone and password are required.'));
            }
        }
        $this->_loginPostRedirect();
    }
	
	
	

	
	  public function editPostAction()
    {
		
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getSession()->getCustomer();

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_edit')
                ->setEntity($customer);
			
			
			$_post = $this->getRequest()->getPost();
			$firstname = $this->getRequest()->getPost('firstname');
			$_post['lastname'] = $firstname;
			$this->getRequest()->setPost($_post); 
			
			
            $customerData = $customerForm->extractData($this->getRequest());

            $errors = array();
            $customerErrors = $customerForm->validateData($customerData);
            if ($customerErrors !== true) {
                $errors = array_merge($customerErrors, $errors);
            } else {
                $customerForm->compactData($customerData);
                $errors = array();

                // If password change was requested then add it to common validation scheme
                if ($this->getRequest()->getParam('change_password')) {
                    $currPass   = $this->getRequest()->getPost('current_password');
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                    if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                        list($_salt, $salt) = explode(':', $oldPass);
                    } else {
                        $salt = false;
                    }

                    if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                        if (strlen($newPass)) {
                            /**
                             * Set entered password and its confirmation - they
                             * will be validated later to match each other and be of right length
                             */
                            $customer->setPassword($newPass);
                            $customer->setConfirmation($confPass);
                        } else {
                            $errors[] = $this->__('New password field cannot be empty.');
                        }
                    } else {
                        $errors[] = $this->__('Invalid current password');
                    }
                }

                // Validate account and compose list of errors if any
                $customerErrors = $customer->validate();
                if (is_array($customerErrors)) {
                    $errors = array_merge($errors, $customerErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $customer->setConfirmation(null);
                $customer->save();
                $this->_getSession()->setCustomer($customer)
                    ->addSuccess($this->__('The account information has been saved.'));

                $this->_redirect('*/*/edit');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        $this->_redirect('*/*/edit');
    }
	
	
	
	
	
	
	
	public function pointAction()
	{
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		
		
		$this->getLayout()->getBlock('head')->setTitle($this->__('Point Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
	
	}
	
	
	public function ecouponAction()
	{
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		
		
		$this->getLayout()->getBlock('head')->setTitle($this->__('Ecoupon Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
		
	
	
	}
	
	
	/**
     * Forgot customer account information page
     */
    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('customer_edit');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
		
		$erp_customer_id_result = Mage::helper('lily')->QueryIdByVipCard($customer->getVipCard());
		$erp_customer_id_result = json_decode($erp_customer_id_result);
		if($erp_customer_id_result[0]->code == 0){
			$erp_data = $erp_customer_id_result[0]->rows;
			// Mage::log($erp_data);
			$erp_id = $erp_data[0][0];
			$vip_name = $erp_data[0][1];
			// Mage::log($vip_name);
			$erp_ponts_ecoupon = Mage::helper('lily')->QueryPointsAmount($erp_id);
			$erp_ponts_ecoupon = json_decode($erp_ponts_ecoupon);

			if($erp_ponts_ecoupon[0]->code == 0){
				$points_data = $erp_ponts_ecoupon[0]->rows;
				$coupon_amount = $points_data[0][0];
				$points_amount = $points_data[0][1];
				$customer->setUsePoints($points_amount);
				$customer->setEcoupon($coupon_amount);
				$customer->setSynchroTime(date('Y-m-d H:i:s'));
				$customer->setVipLevel($vip_name);
				$customer->save();
			}
		
		}
		
		
        if (!empty($data)) {
            $customer->addData($data);
        }
        if ($this->getRequest()->getParam('changepass')==1){
            $customer->setChangePassword(1);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
	
	
	
}
				