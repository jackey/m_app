<?php
// require_once "Mage/Sales/controllers/OrderController.php";
require_once(Mage::getModuleDir('controllers', 'Mage_Sales').'/OrderController.php');
class Mec_Points_Sales_OrderController extends Mage_Sales_OrderController
{

	public function QueryOrderStatusAction()
	{
		$response = array();
		$erp_gift_id = $this->getRequest()->getParam('gift_id');
		$order_id = $this->getRequest()->getParam('order_id');
		
		Mage::log($erp_gift_id);
		// $erp_gift_id = '79';
		$_order = Mage::getModel('sales/order')->load($order_id);
		
		$erp_data = $this->PostErpQueryGift($erp_gift_id, 'all', null);
		if($erp_data['check'] != "" && $_order->getGiftStoreAddress()){
			$response['status'] = $this->__('Complete'); 
			$_order->setErpStatus($response['status'])->save();
			$this->getResponse()->setBody(Zend_Json::encode($response));
			return;
		}
		
		if($_order->getGiftStoreAddress() != ""){
			$erp_transfer_datas = $this->PostErpQueryGift($erp_gift_id, 'transfer', $erp_data);
			if($erp_transfer_datas['out'] == '提交' && $erp_transfer_datas['in'] == '提交'){
				$response['status'] = $this->__('Has been to the store'); 
				$_order->setErpStatus($response['status'])->save();
				$this->getResponse()->setBody(Zend_Json::encode($response));
				return;
			}
			
			if($erp_transfer_datas['out'] == '提交' && $erp_transfer_datas['in'] == '未提交'){
				$response['status'] = $this->__('Out of the warehouse'); 
				$_order->setErpStatus($response['status'])->save();
				$this->getResponse()->setBody(Zend_Json::encode($response));
				return;
			}
			
			if($erp_transfer_datas['out'] == '未提交' && $erp_transfer_datas['in'] == '未提交'){
				$response['status'] = $this->__('Created'); 
				$_order->setErpStatus($response['status'])->save();
				$this->getResponse()->setBody(Zend_Json::encode($response));
				return;
			}
			$response['ship_note'] = "";
		}else{
			$erp_sales_datas = $this->PostErpQueryGift($erp_gift_id, 'sales', $erp_data);
			if($erp_sales_datas['status'] == '提交'){
				$response['status'] = $this->__('Out of the warehouse'); 
				$response['ship_note'] = $erp_sales_datas['ship_note'];
			}else{
				$response['status'] = $this->__('Created'); 
				$response['ship_note'] = "";
			}
			$_order->setErpStatus($response['status'])->save();
			$this->getResponse()->setBody(Zend_Json::encode($response));
			return;
		}
		
		
		
	}
	
	
	protected function PostErpQueryGift($gift_id, $type, $datas)
	{	
	
		$erp_user = Mage::helper('lily')->erpUser();
		$erp_password = Mage::helper('lily')->erpPassword();
		$erp_url = Mage::helper('lily')->erpUrl();
		$post_url = Mage::getBaseUrl();
		
		date_default_timezone_set('Asia/Shanghai');
		$time = date('Y-m-d H:i:s').'.000';
		$sercert = md5($erp_user.$time.md5($erp_password));
		$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
		
		
		switch($type){
			case 'all':
				$query_param_data = array(
					'id' => 1,
					'command' => 'Query',
					'params' => array(
						'table' => 'M_GIFTORDER',
						'columns' => array(
							'TRANSFER_ID',
							'SALE_ID',
							'CHECK_MAN',				
						),
						'params' => array(
							'column' => 'ID',
							'condition' => "{$gift_id}",
						),	
						'range' => 1,
					),	
				);
				break;
			
			case 'transfer':
				$query_param_data = array(
					'id' => 1,
					'command' => 'Query',
					'params' => array(
						'table' => 'M_TRANSFER',
						'columns' => array(
							'OUT_STATUS',
							'IN_STATUS',				
						),
						'params' => array(
							'column' => 'ID',
							'condition' => "{$datas['transfer']}",
						),	
						'range' => 1,
					),	
				);
				break;
				
			case 'sales':	
				$query_param_data = array(
					'id' => 1,
					'command' => 'Query',
					'params' => array(
						'table' => 'M_SALE',
						'columns' => array(
							'STATUS',
							'SHIPPING_REMARK',				
						),
						'params' => array(
							'column' => 'ID',
							'condition' => "{$datas['sales']}",
						),	
						'range' => 1,
					),	
				);
				break;
		}
				
		$query_params = json_encode($query_param_data);
		$post_data .= "&transactions=[{$query_params}]"; 
		
		$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
		$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
		
		Mage::log($result);
		$erp_data = array();
		$result = json_decode($result);
		// Mage::log($result);
		if($result[0]->code == 0){
			$datas = $result[0]->rows;
			switch($type){
				case 'all':
					$erp_data['transfer'] = $datas[0][0];
					$erp_data['sales'] = $datas[0][1];
					$erp_data['check'] = $datas[0][2];
					break;
					
				case 'transfer':
					$erp_data['out'] = $datas[0][0];
					$erp_data['in'] = $datas[0][1];
				    break;
					
				case 'sales':
					$erp_data['status'] = $datas[0][0];
					$erp_data['ship_note'] = $datas[0][1];
					break;
			}
		}
		
		return $erp_data;
	}
	
	
}