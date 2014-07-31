<?php
class Mec_Points_SynchronizationController extends Mage_Adminhtml_Controller_Action{

	public function synAction()
	{
		$collection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSelect('*');
		
		foreach ($collection as $product) {
			$sku_splice_array = explode('-', $product->getSku());
			if(count($sku_splice_array) == 3){
				echo $product->getSku() . '<br />';
				
				$asi_id = Mage::helper('points')->getErpAsiId($sku_splice_array[1], $sku_splice_array[2]);
				if($asi_id){
					//查询库存
					$erp_user = Mage::helper('lily')->erpUser();
					$erp_password = Mage::helper('lily')->erpPassword();
					$erp_url = Mage::helper('lily')->erpUrl();
					
					$post_url = Mage::getBaseUrl();

					date_default_timezone_set('Asia/Shanghai');
					$time = date('Y-m-d H:i:s').'.000';
					$sercert = md5($erp_user.$time.md5($erp_password));
					$post_data = "sip_appkey={$erp_user}&sip_timestamp={$time}&sip_sign={$sercert}";
					
					$query_params = array(
						'id' => 1,
						'command' => 'Query',
						'params' => array(
							'table' => 'V_FA_V_STORAGE1',
							'columns' => array(
								'QTYCAN'
							),
							'params' => array(
								'column' => 'C_STORE_ID;NAME',
								'condition' => 'C会员礼品仓',
								'expr1' => array(
										'column' => 'M_ATTRIBUTESETINSTANCE_ID',
										'condition' => "{$asi_id}",
									),
								'expr2' => array(
									'column' => 'M_PRODUCT_ID;NAME',
									'condition' => "{$sku_splice_array[0]}",
								),
									'combine' => 'and',
							),					
							'range' => 1,				
						),
					);

					$query_params = json_encode($query_params);
					$post_data .= "&transactions=[{$query_params}]"; 
					$header = Mage::helper('lily')->FormatHeader($post_url, $post_data);
					$result = Mage::helper('lily')->PostDataToErp($erp_url, $post_data, $header);
					
					$result = json_decode($result);
					if($result[0]->code == 0){
						$erp_can_qty = $result[0]->rows;
						$erp_can_qty = $erp_can_qty[0][0];
						
						$productId = $product->getId();
						$stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
						$stockItemId = $stockItem->getId();
						$stockItem->setData('qty', (integer)$erp_can_qty);
						$stockItem->save();
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('%s 同步成功, 库存%s', $product->getSku(), (integer)$erp_can_qty));
						// $product->setQty($erp_can_qty)->save();
					}else{
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s 同步失败', $product->getSku()));
					}	
				}else{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s 同步失败, 请检查ASI', $product->getSku()));
				}
			}
				
				
		}
		$this->_redirectReferer();
		return;
	}
	
	
}