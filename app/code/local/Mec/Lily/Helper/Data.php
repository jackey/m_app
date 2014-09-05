<?php
class Mec_Lily_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function FormatHeader($url, $post_data, $myIp = null,$xml = null)
	{
		
		 $temp = parse_url($url);
		 $query = isset($temp['query']) ? $temp['query'] : '';
		 $path = isset($temp['path']) ? $temp['path'] : '/';
		
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$AGENT = $_SERVER['HTTP_USER_AGENT'];
		}else{
			$AGENT = "Mozilla/5.0 (Windows NT 6.1; rv:23.0) Gecko/20100101 Firefox/23.0";
		}
		 $header = array (
			 "Accept-Language: zh-cn",
			 "POST {$path}?{$query} HTTP/1.1",
			 "Host: {$temp['host']}",
			 "Content-Type: application/x-www-form-urlencoded",
			 'Accept: */*',
			 'Accept-Encoding: gzip, deflate',
			 'User-Agent: ' . $AGENT,
			 // "X-Forwarded-For: {$myIp}",
			 "Content-length: " .strlen($post_data),
			 "Connection: Close",
			 "Cache-Control: no-cache"
		 );

		 return $header;
	} 
	
	
	
	public function PostDataToErp($url, $post_data, $header){
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);                                                                  
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                                                                                   

		$result = curl_exec($ch);
		
		return $result;
	
	}
	
	public function verifyCode($length)
	{
		$chars = array( 
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
		); 
		$charsLen = count($chars) - 1; 
	 
		shuffle($chars); 
		 
		$output = ""; 
		for ($i=0; $i<$length; $i++) 
		{ 
			$output .= $chars[mt_rand(0, $charsLen)]; 
		} 
 
		return $output; 
	
	}
	
	
	public function randEmail($length)
	{
		$chars = array( 
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
			"K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
			"U", "V", "W", "X", "Y", "Z",
		); 
		$charsLen = count($chars) - 1; 
	 
		shuffle($chars); 
		 
		$output = ""; 
		for ($i=0; $i<$length; $i++) 
		{ 
			$output .= $chars[mt_rand(0, $charsLen)]; 
		} 
 
		return $output . '@example.com'; 
	
	}
	
	
	
	public function QueryPointsAmount($id)
	{
		
		$erp_user = $this->erpUser();
		$erp_password = $this->erpPassword();
		$erp_url = $this->erpUrl();
		
		$post_url = Mage::getBaseUrl();
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		$query_points = array(
				'id' => 1,
				'command' => 'Query',
				'params' => array(
					'table' => 'FA_VIPACC',
					'columns' => array(
								'AMOUNT',
								'INTEGRAL',
									),
					'params' => array(
								"column"=>"C_VIP_ID",
								"condition" => "{$id}",
					
								),
								
					'range' => 10000,
				),

			);
			
			
			$query_points = json_encode($query_points);
			$post_data .= "&transactions=[{$query_points}]"; 
			
			$header_p = $this->FormatHeader($post_url, $post_data);
			$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header_p);
			
			
			return $result;
	
	}
	
	
	public function QueryEcouponById($v_id)
	{
		$erp_user = $this->erpUser();
		$erp_password = $this->erpPassword();
		$erp_url = $this->erpUrl();
		
		$post_url = Mage::getBaseUrl();
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
	
		$query_ecoupon = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'FA_VIPPAYAMT_CHK',
				'columns' => array(	
							'CREATIONDATE',
							'DOCNO',
							'LOSE_DATE',
							'VIP_PAYAMT',
							"DESCRIPTION"
							
								),
				'params' => array(
					// 'expr1' => array(
						// 'column' => 'C_VIP_ID',
						// 'condition' => "{$v_id}",
						// 'condition' => '232692',
					// ),
					
					// 'expr2' => array(
						// 'column' => 'ISMATCHED',
						// 'condition' => 'Y',
					// ),
					// 'combine' => 'and',
					'column' => 'C_VIP_ID',
					'condition' => "{$v_id}",
				),
				'range' => 10000,
			),
		);
		
		$query_ecoupon = json_encode($query_ecoupon);
		$post_data .= "&transactions=[{$query_ecoupon}]"; 
		
		$header = $this->FormatHeader($post_url, $post_data);
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
		
		return $result;
	}
	
	
	
	public function QueryPointsById($v_id)
	{
		$erp_user = $this->erpUser();
		$erp_password = $this->erpPassword();
		$erp_url = $this->erpUrl();
		
		$post_url = Mage::getBaseUrl();
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		
		$query_points = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'FA_VIPINTEGRAL_FTP',
				'columns' => array(		
							'CREATIONDATE',
							'DESCRIPTION',
							'AMT_ACTUAL',
							'INTEGRAL',
							'DOCNO',
								),
				'params' => array(
					"column"=>"C_VIP_ID",
					"condition" => "{$v_id}",
				),
				
				'range' => 10000,
				'orderby' => array(
					'column' => 'CREATIONDATE',
					'desc' => true,
				),
			),
		);
		
		
		$query_points = json_encode($query_points);
		$post_data .= "&transactions=[{$query_points}]"; 
		
		$header = $this->FormatHeader($post_url, $post_data);
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
		
		Mage::log($result);
		return $result;
	
	}
	
	
	
	
	public function QueryIdByVipCard($card)
	{
		$erp_user = $this->erpUser();
		$erp_password = $this->erpPassword();
		$erp_url = $this->erpUrl();
		
		$post_url = Mage::getBaseUrl();
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		
		
		$query_id = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'C_VIP',
				'columns' => array(
							'ID',
							'C_VIPTYPE_ID;NAME',
							'OPENCARDDATE'
								),
				'params' => array(
							"column"=>"CARDNO",
							"condition" => "{$card}",
							),
							
				'range' => 10000,
			),

		);
			
			
			$query_id = json_encode($query_id);
			$post_data .= "&transactions=[{$query_id}]"; 
			
			$header = $this->FormatHeader($post_url, $post_data);
			$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
			// Mage::log($result);
			
			return $result;
		
		
	
	}
	
	
	
	
	public function erpUser()
	{
		return trim(Mage::getStoreConfig('lily_erp/erp/user'));
	
	}
	
	public function erpPassword()
	{
		return trim(Mage::getStoreConfig('lily_erp/erp/password'));
	}
	
	public function erpUrl()
	{
		return trim(Mage::getStoreConfig('lily_erp/erp/api_url'));
	}
	
	
	public function telIsExist($tel)
	{
		$collection = Mage::getResourceModel('customer/customer_collection')
                 ->addAttributeToSelect('*')
				 ->addAttributeToFilter('telephone', array('eq'=> $tel));
	
		if($collection->getSize() == 0) {
			return true;
		
		}else{
			return false;
		}
	
	}
	
	
	public function getMagentoGender($text)
	{
		$code = "";
		if($text == '男'){
			$code = 123;
		}else{
			$code = 124;
		}
	
		return $code;
	}
	
	
	public function getEmailByTelphone($tel)
	{
		$collection = Mage::getResourceModel('customer/customer_collection')
                 ->addAttributeToSelect('*')
				 ->addAttributeToFilter('telephone', array('eq'=> $tel));
		if($collection->getSize() == 0){
		
			return null;
		}else{
			$email = "";
			foreach($collection as $customer){
				$email = $customer->getEmail();
			
			}
			
			return $email;
		}
	
	}
	
	
	public function SubscribedNewsletter($customer_obj)
	{
	
		$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer_obj->getEmail());
		
		if (!$subscriber->getId() 
			|| $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED 
			|| $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
				
			$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
			$subscriber->setSubscriberEmail($customer_obj->getEmail());
			$subscriber->setSubscriberConfirmCode($subscriber->RandomSequence());
		}

		$subscriber->setStoreId(Mage::app()->getStore()->getId());
		$subscriber->setCustomerId($customer_obj->getId());
			
		try {
			$subscriber->save();
		}
		catch (Exception $e) {
			// throw new Exception($e->getMessage());
			Mage::log($customer_obj->getEmail() . ' ' .$e->getMessage() );
		}
	
	
	
	}
	
	
	public function ErpHasTelephone($telephone)
	{
		$erp_user = $this->erpUser();
		$erp_password = $this->erpPassword();
		$erp_url = $this->erpUrl();
		
		$post_url = Mage::getBaseUrl();
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		$query_telephone = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'C_VIP',
				'columns' => array(
					'ID',
					'CARDNO',
					'VIPNAME',
					'VALIDDATE',
					'EMAIL',
					'POST',
					'ADDRESS',
					'SEX',
					'BIRTHDAY',
					// 'MOBIL',
					'C_VIPTYPE_ID;NAME',
					),
					
					
				'params' => array(
					"column"=>"MOBIL",
					"condition" => "{$telephone}",
				),
				'range' => 10,
			),
		);
		$query_params = json_encode($query_telephone);
		$post_data .= "&transactions=[{$query_params}]";
		
		$header  = $this->FormatHeader($post_url, $post_data);
		$result = $this->PostDataToErp($erp_url, $post_data, $header);
		
		return $result;
	}
	
	public function AddCustomerFromErp($datas, $telephone)
	{
	
		foreach($datas as $data){
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
			$e_tel = $telephone;
			$e_vip_type = $data[9];
			$result_p = $this->QueryPointsAmount($e_id);
			if($result_p != ""){
				$result_p = json_decode($result_p);
				if($result_p[0]->code == 0){
					$points_data = $result_p[0]->rows;
					// Mage::log($points_data);
					$coupon_amount = $points_data[0][0];
					$points_amount = $points_data[0][1];
				}
			
			}
			
			if(ereg("^[-a-zA-Z0-9_.]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$", $e_email) && $e_email != ""){
			
			}else{
				$e_email =  $this->randEmail(10);
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
			}
			
		}
	
	}
	
	
	
	
}
	 