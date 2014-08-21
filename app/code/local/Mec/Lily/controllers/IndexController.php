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
		
		$sign_verify_code = '【Lily商务时装】亲爱的用户，您的手机验证码为：'. $verify_code . '，请把验证码填入注册页面，以便成功注册，期待您的加入！';
		
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
			
			$pass_word_sign = '【Lily商务时装】亲爱的会员，您的登录密码为：' . $password . '，请凭此密码登录Lily网上会员俱乐部，登录后请到账号中心修改密码。';
			
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
			// Mage::log($result, null, 'rikosms.log');
			$_customer = Mage::getModel('customer/customer')->load($customer_id);
			if($result != ""){
				$result = json_decode($result);
				if($result[0]->code == 0 && $result[0]->count == 1){
					
					$_customer->setPassword($password);
					// $_customer->setActivated(1);
					$_customer->save();
					// $this->_redirectSuccess('*');
					$this->_getSession()->addSuccess($this->__('The Password Has Been Sent.'));
					$this->getResponse()->setHeader("Content-Type", "application/json");
					$this->getResponse()->setBody(json_encode(array("redirect" => Mage::getUrl("customer/account/login"))));
					return;
				}else{
					// Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$this->_getSession()->addError($this->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$this->getResponse()->setHeader("Content-Type", "application/json");
					$this->getResponse()->setBody(json_encode(array("redirect" =>"", "message" => $this->__('The %s has Send Sms Fail.', $_customer->getEmail()))));
					return;
					
				}	
			}
		
		}else{
			$this->_getSession()->addError($this->__('You Have No Account In System.'));
			$this->getResponse()->setHeader("Content-Type", "application/json");
			$this->getResponse()->setBody(json_encode(array("redirect" =>"", "message" => $this->__('You Have No Account In System.'))));
			return;
		}
	}
	
	
	
	
	public function getactivatePostAction()
	{
		
		$telphone = $this->getRequest()->getPost('telephone');
		
		if(strlen(trim($telphone)) != 11){
			$this->_getSession()->addError($this->__('Your Telephone Is Wrong.'));
			$this->getResponse()->setHeader("Content-Type", "application/json");
			$this->getResponse()->setBody(json_encode(array("redirect" => "")));
			return;
		}
		
		
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
				$this->getResponse()->setHeader("Content-Type", "application/json");
				$this->getResponse()->setBody(json_encode(array("redirect" => Mage::getUrl("customer/account/edit"))));
				return;
			}else{
				$cutomer_obj->setActivated(1);
				$cutomer_obj->save();
			}
			
			$pass_word_sign = '【Lily商务时装】亲爱的会员，您的登录密码为：' . $password . '，请凭此密码登录Lily网上会员俱乐部，登录后请到账号中心修改密码。';
			
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

					$this->getResponse()->setHeader("Content-Type", "application/json");
					$this->getResponse()->setBody(json_encode(array("redirect" => Mage::getUrl("customer/account/login"))));
					return;
				}else{
					// Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$this->_getSession()->addError($this->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					$this->getResponse()->setHeader("Content-Type", "application/json");
					$this->getResponse()->setBody(json_encode(array("redirect" => "")));
					return;
					
				}	
			}
			
		
		}else{
			$this->IsInErp($telphone);
		}
		// }else{
			// $this->_getSession()->addError($this->__('Your Telephone Is Wrong.'));
			// $this->_redirectReferer();
			// return;
		
		// }
		
	}
	
	public function IsInErp($telphone)
	{
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::helper('core/url')->getCurrentUrl();
	
		
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		$query_is_in_erp_param = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'C_VIP',
				'columns' => array(
					'ID',
					'CARDNO',
					'VIPNAME',
					'ENTERDATE',
					'EMAIL',
					'POST',
					'ADDRESS',
					'SEX',
					'BIRTHDAY',
					'C_VIPTYPE_ID;NAME',
				),
				'params' => array(
					"column"=> "MOBIL",
					"condition" => "={$telphone}",
				),
				'range' => 1,
			),
		);
		
		$query_is_in_erp_param = json_encode($query_is_in_erp_param);
		$post_data .= "&transactions=[{$query_is_in_erp_param}]"; 
		
		$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
		$result_in_erp = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
		$result_in_erp = json_decode($result_in_erp);
		
	
		
		if($result_in_erp != ""){
			if($result_in_erp[0]->code == 0 && count($result_in_erp[0]->rows) > 0){
				$data = $result_in_erp[0]->rows;
				$data = $data[0];
				$customer_obj = Mage::getModel('customer/customer');
				
				$e_id = $data[0];
				$e_card = $data[1];
				$e_name = $data[2];
				$e_card_valid = $data[3];
				$e_email = $data[4];
				$e_zip = $data[5];
				$e_address = $data[6];
				$e_sex = $data[7];
				$e_dob = $data[8];
				$e_tel = $telphone;
				$e_vip_type = $data[9];
				
				$result_p = Mage::helper('lily')->QueryPointsAmount($e_id);
				if($result_p != ""){
					$result_p = json_decode($result_p);
					if($result_p[0]->code == 0){
						$points_data = $result_p[0]->rows;
						$coupon_amount = $points_data[0][0];
						$points_amount = $points_data[0][1];
					}
				}
			
			
				Mage::log($e_email, null, 'riko.log');
				if(ereg("^[-a-zA-Z0-9_.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$", $e_email) && $e_email != ""){
					$_customer = $customer_obj->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($e_email);
					if($_customer->getId()){
						$e_email = Mage::helper('lily')->randEmail(10);
					}else{
						$is_subscribe = true;
					}	
				}else {
					$e_email = Mage::helper('lily')->randEmail(10);
				}
				
				$pass_word  = $customer_obj->generatePassword(6);
				
				$customer_obj->setEmail($e_email);
				$customer_obj->setFirstname($e_name);
				$customer_obj->setLastname($e_name);
				$customer_obj->setPassword($pass_word);
				$customer_obj->setTelephone($e_tel);
				if($e_dob){
					$customer_obj->setDob(date('Y-m-d H:i:s', strtotime($e_dob)));
				}
				$customer_obj->setGender(Mage::helper('lily')->getMagentoGender($e_sex));
				$customer_obj->setSynchro(1);
				$customer_obj->setSynchroTime(date('Y-m-d H:i:s'));
				$customer_obj->setVipCard($e_card);
				$customer_obj->setCardValidity($e_card_valid);
				$customer_obj->setCustomerZip($e_zip);
				$customer_obj->setCustomerAddress($e_address);
				$customer_obj->setVipLevel($e_vip_type);
				if(isset($coupon_amount)){
					$customer_obj->setEcoupon($coupon_amount);
				}
				if(isset($points_amount)){
					$customer_obj->setUsePoints($points_amount);
				}
				try {
					$customer_obj->save();
					$customer_obj->setConfirmation(null);
					// $customer_obj->setData('group_id', $_customer['custgroupid'] + 1); 
					$customer_obj->save();
					if($is_subscribe){
						Mage::helper('lily')->SubscribedNewsletter($customer_obj);
					}
					//Make a "login" of new customer
					// Mage::getSingleton('customer/session')->loginById($customer->getId());
				}
				catch (Exception $ex) {
					//Zend_Debug::dump($ex->getMessage());
					Mage::log($customer_obj->getEmail() . ' ' .$ex->getMessage() );
				}
				
				$customer_id = $customer_obj->getId();
				$pass_word_sign = '【Lily商务时装】亲爱的会员，您的登录密码为：' . $pass_word . '，请凭此密码登录Lily网上会员俱乐部，登录后请到账号中心修改密码。';
				$query_params = array(
					'id' => 1,
					'command' => 'nds.monitor.ext.SendSMS',
					'params' => array(
									'phone' => $telphone,
									'content' => $pass_word_sign,
								)
				);
				
				$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
				$query_params = json_encode($query_params);
				$other_post_data = "&transactions=[{$query_params}]";  
				$other_post_data = $post_data . $other_post_data;
				
				$header = Mage::helper('lily')->FormatHeader($post_url, $other_post_data);
				$result = Mage::helper('lily')->PostDataToErp($erp_url, $other_post_data, $header);
				if($result != ""){
					$result = json_decode($result);
					if($result[0]->code == 0 && $result[0]->count == 1){
						$_customer = Mage::getModel('customer/customer')->load($customer_id);
						$_customer->setActivated(1);
						$_customer->save();
						
						$this->_getSession()->addSuccess($this->__('The %s has been Activated.', $_customer->getEmail()));
						$url = Mage::getBaseUrl() . '#club';
						$this->_redirectUrl($url);
						return;
					}else{
						$this->_getSession()->addError($this->__('The %s has Send Sms Fail.', $_customer->getEmail()));
						$url = Mage::getBaseUrl() . '#club';
						$this->_redirectUrl($url);
						return;
						
					}	
				}
				
				
			}else{
				$this->_getSession()->addError($this->__('Your Telephone Is Wrong.'));
				$this->getResponse()->setHeader("Content-Type", "application/json");
				$this->getResponse()->setBody(json_encode(array("redirect" => "")));
				return;
			}
			
		}else{
			$this->_getSession()->addError('Post Erp Is Fail');
			$this->getResponse()->setHeader("Content-Type", "application/json");
			$this->getResponse()->setBody(json_encode(array("redirect" => "")));
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