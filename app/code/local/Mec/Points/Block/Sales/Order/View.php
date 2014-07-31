<?php
class Mec_Points_Block_Sales_Order_View extends Mage_Sales_Block_Order_View
{
	

	public function getTotalQty()
	{
		$total_qty = 0;
		$items = $this->getOrder()->getAllItems();
		foreach($items as $item){
			if($item->getParentItemId()){
				continue;
			}else{
				$total_qty += $item->getQtyOrdered();
			}
		
		}
		
		return $total_qty ;
	}
	
	
	
	 public function getTotalPoints()
   {
		$total_points = 0;
		$custmer_for_catalog_code = Mage::helper('points')->CustomerTypeCode(Mage::getSingleton('customer/session')->getCustomer()->getVipLevel());
		$items = $this->getOrder()->getAllItems();
		
		foreach($items as $item){
			if($item->getParentItemId()){
				continue;
			}else{
				$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
				$total_points += $_product->getData($custmer_for_catalog_code) * $item->getQtyOrdered();
			}
		
		}
		
		return $total_points;
   }
}
