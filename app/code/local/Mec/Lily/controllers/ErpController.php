<?php
class Mec_Lily_ErpController extends Mage_Adminhtml_Controller_Action{

	public function activateAction(){
	
		$customers = $this->getRequest()->getPost('customer');
		
		$customer_collection =  Mage::getResourceModel('customer/customer_collection')
                 ->addAttributeToSelect('*')
				 ->addAttributeToFilter('activated', array('eq'=> 0))
				 ->addAttributeToFilter('entity_id', array('in'=> $customers));
		
		
		// var_dump($customer_collection->getSize());
		if($customer_collection->getSize() > 0){
			
			$erp_user = Mage::helper('lily')->erpUser();
			$erp_password = Mage::helper('lily')->erpPassword();
			$erp_url = Mage::helper('lily')->erpUrl();
			$post_url = Mage::helper('core/url')->getCurrentUrl();
			
			
			date_default_timezone_set('Asia/Shanghai');
			$time = date('Y-m-d H:i:s').'.000';
			$sercert = md5($erp_user.$time.md5($erp_password));
			$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
			
			
			foreach($customer_collection as $_customer){
				$tel = $_customer->getTelephone();
				$pass_word  = $_customer->generatePassword(6);	
				$pass_word_sign = $pass_word . '【Lily商务时装】';
			
				
				$query_params = array(
					'id' => 1,
					'command' => 'nds.monitor.ext.SendSMS',
					'params' => array(
									'phone' => $tel,
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
						$_customer->setPassword($pass_word);
						// $_customer->setActivated(1);
						$_customer->save();
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The %s has been Activated.', $_customer->getEmail()));
					}else{
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The %s has Send Sms Fail.', $_customer->getEmail()));
					}	
				}
			}
			
			$this->_redirectReferer();
			return;
		}else{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Selected Customers Is Already Activated.'));
			$this->_redirectReferer();
			return;
		}
		
	}
	
	
	public function synchronizationCardAction()
	{
	
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::helper('core/url')->getCurrentUrl();
		
		if($erp_user == "" || $erp_password == "" || $erp_url == ""){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('User Name, PassWord, Api Url Are Required'));
			$this->_redirectReferer();
			return;
		}
		
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		
		$query_params = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
							'table' => 'C_VIP',
							'columns' => array(
										'CARDNO',
										'VALIDDATE',
											),
							'params' => array(
								'expr1' => array(
									'expr1' => array(
										'column' => 'C_VIPTYPE_ID;NAME',
										// 'condition' => '网络会员',
										'condition' => '粉丝',
									),
									'expr2' => array(
										'column' => 'OPENCARD_STATUS',
										'condition' => '1',
									),
									'combine' => 'and',
								),
								
								'expr2' => array(
									'column' => 'MOBIL',
									'condition' => 'is null',
								),
								'combine' => 'and',
							),
							'range' => 10000,
						)
		);
		
		$query_params = json_encode($query_params);
		$post_data .= "&transactions=[{$query_params}]";  
		
		
		$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
		// Mage::log($result);
		if($result != ""){
			$result = json_decode($result);
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$writeConnection = $resource->getConnection('core_write');
			$table = 'lily_vip_card';
			$count = 0;
			if($result[0]->code == 0){
				$vip_cards = $result[0]->rows;
				foreach($vip_cards as $vip_card){
					$search_card = $readConnection->fetchOne("SELECT `id` FROM `lily_vip_card` WHERE `vip_card` = '{$vip_card[0]}' ");
					Mage::log($vip_card);
					Mage::log($search_card);
					if($search_card == ""){
						$date_time = date('Y-m-d', strtotime($vip_card[1]));
						$insert_query = "INSERT INTO `lily_vip_card`(`id`, `vip_card`, `customer_id`, `valid_date`) VALUES ('', '{$vip_card[0]}', '', '{$date_time}')";
						$writeConnection->query($insert_query);
						$count++;
					}
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The %s has been Import.', $count));
				$this->_redirectReferer();
				return;
			}else{	
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s', $result[0]->message));
				$this->_redirectReferer();
				return;
			}
		
		}
		
		
	}
	
	
	public function manualSYNCAction()
	{		
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::helper('core/url')->getCurrentUrl();
		
		if($erp_user == "" || $erp_password == "" || $erp_url == ""){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('User Name, PassWord, Api Url Are Required'));
			$this->_redirectReferer();
			return;
		}
		
		$customers = $this->getRequest()->getPost('customer');
		
		foreach($customers as $_id){
			$_customer = Mage::getModel('customer/customer')->load($_id);
			$erp_customer_id_result = Mage::helper('lily')->QueryIdByVipCard($_customer->getVipCard());
			$erp_customer_id_result = json_decode($erp_customer_id_result);
			if($erp_customer_id_result[0]->code == 0){
				$erp_id = $erp_customer_id_result[0]->rows;
				$erp_id = $erp_id[0][0];
				$erp_ponts_ecoupon = Mage::helper('lily')->QueryPointsAmount($erp_id);
				$erp_ponts_ecoupon = json_decode($erp_ponts_ecoupon);
			
				if($erp_ponts_ecoupon[0]->code == 0){
					$points_data = $erp_ponts_ecoupon[0]->rows;
					$coupon_amount = $points_data[0][0];
					$points_amount = $points_data[0][1];
					$_customer->setUsePoints($points_amount);
					$_customer->setEcoupon($coupon_amount);
					$_customer->setSynchroTime(date('Y-m-d H:i:s'));
					$_customer->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('%s 同步成功', $_customer->getFirstname()));
				}else{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s 同步失败', $_customer->getFirstname()));
				}
				
				
			}else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s 同步失败', $_customer->getFirstname()));
			}
			
		}
		
		
		$this->_redirectReferer();
		return;
	}
	
	
	public function manualSYNCUpdateAction()
	{
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::helper('core/url')->getCurrentUrl();
		
		if($erp_user == "" || $erp_password == "" || $erp_url == ""){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('User Name, PassWord, Api Url Are Required'));
			$this->_redirectReferer();
			return;
		}
	
		$customers = $this->getRequest()->getPost('customer');
		foreach($customers as $_id){
			$_customer = Mage::getModel('customer/customer')->load($_id);
			$customer_email = $_customer->getEmail();
			$customer_name = $_customer->getFirstname();
			$customer_telephone = $_customer->getTelephone();
			$customer_address = $_customer->getCustomerAddress();
			$customer_zip = $_customer->getCustomerZip();
			$customer_birth = $_customer->getDob();
			$customer_birth = date('Ymd', strtotime($customer_birth));
			$customer_gender = $_customer->getGender();
			$lily_vip_card = $_customer->getVipCard();
			if($customer_gender == 123){
				$customer_gender = '男';
			}else{
				$customer_gender = '女';
			}
			
			date_default_timezone_set('Asia/Shanghai');
			$time = date('Y-m-d H:i:s').'.000';
			$sercert = md5($erp_user.$time.md5($erp_password));
			$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
			
			$query_params = array(
				'id' => 1,
				'command' => 'ObjectModify',
				'params' => array(
					'table' => 'C_VIP',
					'partial_update' => true,
					'ak' => "{$lily_vip_card}",
					'VIPNAME' => "{$customer_name}",
					'POST' => "{$customer_zip}",
					'ADDRESS' => "{$customer_address}",
					'MOBIL' => "{$customer_telephone}",
					'EMAIL' => "{$customer_email}",
					'BIRTHDAY' => "{$customer_birth}",
					'SEX' => "{$customer_gender}",
				)
			);
			
			$query_params = json_encode($query_params);
			$post_data .= "&transactions=[{$query_params}]"; 
			Mage::log($post_data);
			
			
			$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
			$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);

			Mage::log($result);
			
			if($result != ""){
				$result = json_decode($result);
				if($result[0]->code == 0){
					$_customer->setSynchroTime(date("Y-m-d H:i:s"))->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('%s 同步成功', $_customer->getFirstname()));
				}else{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s 同步失败', $_customer->getFirstname()));
				}
			}else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s 同步失败', $_customer->getFirstname()));
			}	
			
		}
		
		$this->_redirectReferer();
		return;
		
	}
	
	
	
	
}