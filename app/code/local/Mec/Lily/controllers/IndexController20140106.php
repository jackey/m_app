<?php
class Mec_Lily_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout(); 
	  
    }
	
	public function verifyCodeAction()
	{
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
		
		$_code = $this->getRequest()->getParam('code');
		
		$verify_code = Mage::getSingleton('core/session')->getVerify();
		if($_code == $verify_code){
			$response['success'] = true;
		}
		
		$this->getResponse()->setBody(Zend_Json::encode($response));
	}
	
	
	
	public function sendVerifyfCodeAction()
	{
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
		
		$telephone = $this->getRequest()->getParam('tel');
		$post_url = $this->getRequest()->getParam('url');
		$verify_code = Mage::helper('lily')->verifyCode(4); 
		
		
		
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		
		
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		
		$sign_verify_code = '亲爱的用户，您的手机验证码为：'. $verify_code . '，请把该验证码填入注册页面，以便成功注册，期待您的加入！【Lily女装】';
		
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		$query_params = array(
			'id' => 1,
			'command' => 'nds.monitor.ext.SendSMS',
			'params' => array(
							'phone' => $telephone,
							'content' => $sign_verify_code,
						)
		);
		
		$query_params = json_encode($query_params);
		$post_data .= "&transactions=[{$query_params}]";  
		
		$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
		
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
		Mage::log($post_data);
		Mage::log($result);
		if($result != ""){
			$result = json_decode($result);
			if($result[0]->code == 0 && $result[0]->count == 1){
				Mage::getSingleton('core/session')->setVerify($verify_code);
				$response['success'] = true;
			}
		}
		
		
		$this->getResponse()->setBody(Zend_Json::encode($response));
	}
	
	
	public function activateAction(){
		$this->loadLayout();  
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout(); 
	}
	
	
	public function getactivateAction()
	{
		if($this->_getSession()->isLoggedIn())
		{
			$this->_redirect('customer/account/edit/');
			return;
		}
		$this->loadLayout();  
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout(); 

	}
	
	
	public function forgotpasswordAction()
	{
		$telphone = $this->getRequest()->getPost('telephone');
		$collection = Mage::getResourceModel('customer/customer_collection')
                 ->addAttributeToSelect('*')
				 ->addAttributeToFilter('activated', array('eq'=> 1))
				 ->addAttributeToFilter('telephone', array('eq'=> $telphone));
		
		
		if($collection->getSize() > 0){
			$email = "";
			
			$password = "";
			$customer_id = "";
			foreach($collection as $customer){
				$customer_id = $customer->getId();
				$email = $customer->getEmail();
				
				$password = $customer->generatePassword(6);
				break;
			}
			
			$pass_word_sign = '亲爱的会员，您的登录密码为：' . $password . '，欢迎登录lily网上会员俱乐部！【Lily女装】';
			
			$erp_user = Mage::helper('lily')->erpUser();
			$erp_password = Mage::helper('lily')->erpPassword();
			$erp_url = Mage::helper('lily')->erpUrl();
			$post_url = Mage::helper('core/url')->getCurrentUrl();
			
			
			date_default_timezone_set('Asia/Shanghai');
			$time = date('Y-m-d H:i:s').'.000';
			$sercert = md5($erp_user.$time.md5($erp_password));
			$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
			
			$query_params = array(
					'id' => 1,
					'command' => 'nds.monitor.ext.SendSMS',
					'params' => array(
									'phone' => $telphone,
									'content' => $pass_word_sign,
								)
				);
			$query_params = json_encode($query_params);
			$other_post_data = "&transactions=[{$query_params}]";  
			$other_post_data = $post_data . $other_post_data;
			
			$header = Mage::helper('lily')->FormatHeader($post_url, $other_post_data);
			$result = Mage::helper('lily')->PostDataToErp($erp_url, $other_post_data, $header);
			
			if($result != ""){
				$result = json_decode($result);
				if($result[0]->code == 0 && $result[0]->count == 1){
					$_customer = Mage::getModel('customer/customer')->load($customer_id);
					$_customer->setPassword($password);
					// $_customer->setActivated(1);
					$_customer->save();
					// $this->_redirectSuccess('*');
					$this->_getSession()->addSuccess($this->__('The Password Has Been Sent.'));
					$url = Mage::getBaseUrl() . '#club';
					$this->_redirectUrl($url);
					return;
				}else{
					// Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$this->_getSession()->addError($this->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$url = Mage::getBaseUrl() . '#club';
					$this->_redirectUrl($url);
					return;
					
				}	
			}
		
		}else{
			$this->_getSession()->addError($this->__('You Have No Account In System.'));
			$this->_redirectReferer();
			return;
		}
	}
	
	
	
	
	public function getactivatePostAction()
	{
		
		$telphone = $this->getRequest()->getPost('telephone');
		$collection = Mage::getResourceModel('customer/customer_collection')
                 ->addAttributeToSelect('*')
				 // ->addAttributeToFilter('activated', array('eq'=> 0))
				 ->addAttributeToFilter('telephone', array('eq'=> $telphone));
		
		if($collection->getSize() > 0){
			$email = "";
			
			$password = "";
			$customer_id = "";
			foreach($collection as $customer){
				$customer_id = $customer->getId();
				$email = $customer->getEmail();
				
				$password = $customer->generatePassword(6);
				break;
			}
			
			
			$cutomer_obj = Mage::getModel('customer/customer')->load($customer_id);
			$actived = $cutomer_obj->getActivated();
			
			
			if($actived == 1){
				$this->_getSession()->addError($this->__('You Are Online '));
				$url = Mage::getBaseUrl() . '#club';
				$this->_redirectUrl($url);
				return;
			}else{
				$cutomer_obj->setActivated(1);
				$cutomer_obj->save();
			}
			
			$pass_word_sign = '亲爱的会员，您的登录密码为：' . $password . '，欢迎登录lily网上会员俱乐部！【Lily女装】';
			
			$erp_user = Mage::helper('lily')->erpUser();
			$erp_password = Mage::helper('lily')->erpPassword();
			$erp_url = Mage::helper('lily')->erpUrl();
			$post_url = Mage::helper('core/url')->getCurrentUrl();
			
			
			date_default_timezone_set('Asia/Shanghai');
			$time = date('Y-m-d H:i:s').'.000';
			$sercert = md5($erp_user.$time.md5($erp_password));
			$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
			
			$query_params = array(
					'id' => 1,
					'command' => 'nds.monitor.ext.SendSMS',
					'params' => array(
									'phone' => $telphone,
									'content' => $pass_word_sign,
								)
				);
			$query_params = json_encode($query_params);
			$other_post_data = "&transactions=[{$query_params}]";  
			$other_post_data = $post_data . $other_post_data;
			
			$header = Mage::helper('lily')->FormatHeader($post_url, $other_post_data);
			$result = Mage::helper('lily')->PostDataToErp($erp_url, $other_post_data, $header);
			
			if($result != ""){
				$result = json_decode($result);
				if($result[0]->code == 0 && $result[0]->count == 1){
					$_customer = Mage::getModel('customer/customer')->load($customer_id);
					$_customer->setPassword($password);
					$_customer->setActivated(1);
					// $_customer->setActivated(1);
					$_customer->save();
					
					$this->_getSession()->addSuccess($this->__('The %s has been Activated.', $_customer->getEmail()));
					// $this->_redirectSuccess('*');
					$url = Mage::getBaseUrl() . '#club';
					$this->_redirectUrl($url);
					return;
				}else{
					// Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$this->_getSession()->addError($this->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$url = Mage::getBaseUrl() . '#club';
					$this->_redirectUrl($url);
					return;
					
				}	
			}
			
		
		}else{
			$this->_getSession()->addError($this->__('Your Telephone Is Wrong.'));
			$this->_redirectReferer();
			return;
		
		}
		
	}
	
	
	public function activateinfoAction()
	{	
		if(Mage::getSingleton('core/session')->getActivateVerify() != 1 && !$this->_getSession()->isLoggedIn()){
			$this->_redirect('lily/index/activate');
            return;
		}
		
		if($this->_getSession()->isLoggedIn() && Mage::getSingleton('core/session')->getActivateVerify() != 1){
		
			$this->_redirect('customer/account/create/');
            return;
		}
		
		$this->loadLayout();  
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout(); 
	}
	
	
	 protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
	
	public function activatePostAction()
	{
		
		$session = $this->_getSession();
		$telphone = $this->getRequest()->getPost('telephone');
		$active_code = $this->getRequest()->getPost('activate_code');
		
		$collection = Mage::getResourceModel('customer/customer_collection')
                 ->addAttributeToSelect('*')
				 ->addAttributeToFilter('activated', array('eq'=> 0))
				 ->addAttributeToFilter('telephone', array('eq'=> $telphone));
				 
		
		
		if($collection->getSize() > 0){
			$email = "";
			$id = "";
			foreach($collection as $customer){
				$email = $customer->getEmail();
				$id = $customer->getId();
				break;
			}
			try {
				$session->login($email, $active_code);
				$_customer = Mage::getModel('customer/customer')->load($id); 
				$_customer->setActivated(1); 
				$_customer->save();
				Mage::getSingleton('core/session')->setActivateVerify(1);
				$this->_redirect('lily/index/activateinfo');
				
			}catch (Mage_Core_Exception $e) {
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
					$this->_redirectError(Mage::getUrl('lily/index/activate/', array('_secure' => true)));
					return;
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
				
		}else{
			 $session->addError($this->__('Wrong Telephone.'));
		     $this->_redirectError(Mage::getUrl('lily/index/activate/', array('_secure' => true)));
			 return;
		}
		
	}
	
	public function activateInfoPostAction()
	{
	
		$session = $this->_getSession();
		if ($this->getRequest()->isPost()) {
			 $customer = $this->_getSession()->getCustomer();
			 $customerForm = Mage::getModel('customer/form');
			 $customerForm->setFormCode('form-activate-info')
                ->setEntity($customer);
			
			if($customer->getEmail() !=  $this->getRequest()->getPost('email')){
				$customer->setEmail($this->getRequest()->getPost('email'));
				$customer->setPassword($this->getRequest()->getPost('password'));
				$customer->setConfirmation($this->getRequest()->getPost('confirmation'));
				$customer->setCustomerAddress($this->getRequest()->getPost('customer_address'));
			}else{
				$customer->setPassword($this->getRequest()->getPost('password'));
				$customer->setConfirmation($this->getRequest()->getPost('confirmation'));
				$customer->setCustomerAddress($this->getRequest()->getPost('customer_address'));
			}
			
			try {
				$customer->save();
				Mage::getSingleton('core/session')->unsActivateVerify();
				$this->_redirect('customer/account');
			}catch (Mage_Core_Exception $e){
				$session->addError($e->getMessage());
				$this->_redirectError(Mage::getUrl('lily/index/activate/', array('_secure' => true)));
				return;
			}		
		}else{
			$this->_redirect('lily/index/activateinfo');
		}
		
		
		
	
	
	}
	
	
	
	
	
}