<?php
class Mec_Points_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	
	}


	public function CustomerTypeCode($dengji){
		
		$type_array = array(
			'粉丝' => 'lily_fans',
			'会员' => 'lily_member',
			'VIP' => 'lily_vip'
		);
		
		return $type_array[$dengji];
	}
	
	
	
	
	
	public function CanAddThisProductByPoints($product, $qty)
	{
		$can_add = false;
		
		$points_of_customer = $this->getCustomer()->getUsePoints();
		if($points_of_customer == "" || !isset($points_of_customer)){
			$points_of_customer = 0;
		}
		
		$catalog_point_code = $this->CustomerTypeCode($this->getCustomer()->getVipLevel());
		
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quote_points = 0;
		if($quote){
			$cartItems = $quote->getAllItems();
			foreach($cartItems as $item){
				if($item->getParentItemId()){
					continue;
				}else{
					$_product = Mage::getModel('catalog/product')->load($item->getProductId());
					$quote_points += $_product->getData($catalog_point_code) * $item->getQty();
				}
				
			}
			
		}
		$quote_points += $product->getData($catalog_point_code) * $qty;
		// Mage::log($quote_points);
		// Mage::log($points_of_customer);
		if($points_of_customer >= $quote_points){
			$can_add = true;
		}
		
		return $can_add;
		
	}
	
	
	
	public function getthemonth($date) 
	{ 
		$firstday = date('Y-m-01', strtotime($date)); 
		$lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day")); 
		return array($firstday, $lastday); 
	} 
		
	public function PostErpGetCanPlaceInMonth($date)
	{
		$has_gift_in_this_month = false;
	
		$_customer = $this->getCustomer();
		$vip_card_num = $_customer->getVipCard();
		
		
		$search_time_h = date("Y/m/d H:i:s",strtotime($date[0]));
		$search_time_e = date("Y/m/d H:i:s",strtotime($date[1]));
		
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::getBaseUrl();
		
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		$query_datas_params = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'M_GIFTORDER',
				'columns' => array(
					'DOCNO'
				),
				'params' => array(
					
					'expr1' => array(
						'expr1' => array(
							'column' => 'STATUSTIME',
							'condition' => ">{$search_time_h}",
						),
						'expr2' => array(
							'column' => 'STATUSTIME',
							'condition' => "<{$search_time_e}",
						),
						'combine' => 'and',
					),
					
					'expr2' => array(
						'column' => 'VIP_ID;CARDNO',
						'condition' => "{$vip_card_num}",
						// 'condition' => "546061",
					),
					'combine' => 'and',
				),
				'range' => 100,
			),
		
		);
		
		$query_datas_params = json_encode($query_datas_params);
		$post_datas = $post_data . "&transactions=[{$query_datas_params}]"; 
		
		// Mage::log($post_datas);
		$header = Mage::helper('lily')->FormatHeader($post_url, $post_datas);
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_datas, $header);
		
	
		$result = json_decode($result);
		if($result[0]->code == 0){
			$data = $result[0]->rows;
			// Mage::log($data);
			if(count($data) > 0){
				$has_gift_in_this_month = true;
			}
		}else{
			$has_gift_in_this_month = true;
		}
		
		return $has_gift_in_this_month;
		// return false;
	}
	
	
	
	
	
	public function PostErpToGetDatas($erp_gift_order_id)
	{
		$datas = array();
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::getBaseUrl();
		
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		$query_datas_params = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'M_GIFTORDER',
				'columns' => array(
					'IDENTIFIER', 'SALE_ID'
				),
				'params' => array(
					'column' => 'ID',
					'condition' => "{$erp_gift_order_id}",
				),
				'range' => 1,
			),
		
		);
		
		$query_datas_params = json_encode($query_datas_params);
		$post_datas = $post_data . "&transactions=[{$query_datas_params}]"; 
		
		$header = Mage::helper('lily')->FormatHeader($post_url, $post_datas);
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_datas, $header);
		
		$result = json_decode($result);
		if($result[0]->code == 0){
			$erp_datas = $result[0]->rows;
			$erp_datas = $erp_datas[0];
			$datas['ver_code'] = $erp_datas[0];
			$datas['erp_sales_id'] = $erp_datas[1];
		}
		
		// Mage::log($datas);
		return $datas;
	}
	
	
	
	
	public function getErpAsiId($value1, $value2)
	{
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::getBaseUrl();


		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		
		$query_asi_params = array(
			'id' => 1,
			'command' => 'Query',
			'params' => array(
				'table' => 'M_ATTRIBUTESETINSTANCE',
				'columns' => array(
					'ID'
				),
				'params' => array(
					'expr1' => array(
							'column' => 'VALUE1',
							'condition' => "{$value1}",
						),
					'expr2' => array(
							'column' => 'VALUE2_CODE',
							'condition' => "{$value2}",
						),
					'combine' => 'and',
					
				),
			'range' => 1,
			),

		);
		
		$query_asi_id_params = json_encode($query_asi_params);
		$post_asi_data = $post_data . "&transactions=[{$query_asi_id_params}]"; 
		
		// Mage::log($post_asi_data);
		$header_asi = Mage::helper('lily')->FormatHeader($post_url, $post_asi_data);
		$result_asi = Mage::helper('lily')->PostDataToErp($erp_url, $post_asi_data, $header_asi);
		
		$result_asi = json_decode($result_asi);
		if($result_asi[0]->code == 0){
			$asi_id = $result_asi[0]->rows;
			$asi_id = $asi_id[0][0];
		}else{
			$asi_id = null;
		}
		
		// Mage::log($asi_id);
		
		return $asi_id;
		
	}
	
	
	
	public function getSizeText($id)
	{
		$size_array = array(
			0 => 'XS',
			1 => 'S',
			2 => 'M',
			3 => 'L',
			4 => 'XL',
			5 => 'XXL',
			6 => 'XXXL'
		);
		
		return $size_array[$id];
	}
	
	
	 public function getTotalPoints()
   {
		$total_points = 0;
		$custmer_for_catalog_code = $this->CustomerTypeCode($this->getCustomer()->getVipLevel());
		$items = Mage::getSingleton('checkout/type_onepage')->getQuote()->getAllItems();
		foreach($items as $item){
			if($item->getParentItemId()){
				continue;
			}else{
				$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
				$total_points += $_product->getData($custmer_for_catalog_code) * $item->getQty();
			}
		
		}
		
		return $total_points;
   }
	
	
	
	public function CanUpdateGiftProduct($update_cart_data)
	{
		$can_update = false;
		$customer_points = $this->getCustomer()->getUsePoints();
		$custmer_for_catalog_code = $this->CustomerTypeCode($this->getCustomer()->getVipLevel());
		$quote_points = 0;
		$quote_items = Mage::getSingleton('checkout/type_onepage')->getQuote()->getAllItems();
		
		foreach($quote_items as $item){
			if($item->getParentItemId()){
				continue;
			}else{
				foreach($update_cart_data as $index => $data){
					if($item->getItemId() == $index){
						$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
						$quote_points += $_product->getData($custmer_for_catalog_code) * $data['qty'];
					}
				}
			}
		
		}
		
		if($customer_points - $quote_points >= 0){
			$can_update = true;
		}
	
		return $can_update;
	}
	
	
	public function getProductPoints($product)
	{
		$custmer_for_catalog_code = $this->CustomerTypeCode($this->getCustomer()->getVipLevel());
		$product = Mage::getModel('catalog/product')->load($product->getId());
		
		return $product->getData($custmer_for_catalog_code);
	
	}
	
	public function productInWishlist($id)
	{
		$flag = false;
		 $_itemCollection = Mage::helper('wishlist')->getWishlistItemCollection();
		 foreach ($_itemCollection as $_item) {
			if($_item->getProductId() == $id){
				$flag = true;
				break;
			}
		 
		 }
		
		return $flag;
	
	
	}
	
	
	
}
	 