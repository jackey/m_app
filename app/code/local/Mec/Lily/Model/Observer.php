<?php
class Mec_Lily_Model_Observer{

	//Bind For Customer Vip Card
	public function cardBind($observer)
	{
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');
		
		$customer = $observer->getCustomer();
		$customer_id = $customer->getId();
		$customer_email = $customer->getEmail();
		$customer_name = $customer->getName();
		$customer_telephone = $customer->getTelephone();
		$customer_address = $customer->getCustomerAddress();
		$customer_zip = $customer->getCustomerZip();
		$customer_birth = $customer->getDob();
		$customer_birth = date('Ymd', strtotime($customer_birth));
		$customer_gender = $customer->getGender();
		if($customer_gender == 123){
			$customer_gender = '男';
		}else{
			$customer_gender = '女';
		}
		
		Mage::log('cardBind');
		Mage::log($customer_birth);
		Mage::log($customer_gender);
		
		
		$customer->setActivated(1)->save();
		
		$lily_vip_card = $readConnection->fetchOne("SELECT `vip_card` FROM `lily_vip_card` WHERE `customer_id` =0 LIMIT 0 , 1");
		Mage::log("Lily VIP CARD: ".$lily_vip_card);
		if($lily_vip_card != ""){
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
			Mage::log("Post Data: ");
			Mage::log($post_data);
			
			// Mage::log($post_data);
			$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
			$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
			
			Mage::log($result);
			
			if($result != "") {
				$result = json_decode($result);
				if($result[0]->code == 0){
					$card_vailate = $readConnection->fetchOne("SELECT `valid_date` FROM `lily_vip_card` WHERE `vip_card` = '{$lily_vip_card}'");
					$writeConnection->query("UPDATE `lily_vip_card` SET `customer_id` = {$customer_id} WHERE `vip_card` = '{$lily_vip_card}' ");
					$customer->setCardValidity($card_vailate);
					$customer->setVipCard($lily_vip_card);
					$customer->save();
				}
			}
			
		}
		
	}	

	
	
	public function addMassaction($observer)
	{
		$block = $observer->getEvent()->getBlock();
		
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'customer') 
        {
             $block->addItem('lily', array(
                'label' => 'Activate Customer',
                'url' => Mage::app()->getStore()->getUrl('lilyadmin/erp/activate'),
            ));
			
			$block->addItem('lily', array(
                'label' => '手动同步',
                'url' => Mage::app()->getStore()->getUrl('lilyadmin/erp/manualSYNC'),
            ));
			
			$block->addItem('lily', array(
                'label' => '同步更新Erp',
                'url' => Mage::app()->getStore()->getUrl('lilyadmin/erp/manualSYNCUpdate'),
            ));
        }
	
	}
}