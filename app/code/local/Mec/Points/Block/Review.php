<?php   
class Mec_Points_Block_Review extends Mage_Core_Block_Template{   

	public function getItems()
	{
		return Mage::getSingleton('checkout/type_onepage')->getQuote()->getAllItems();
	}

	
	public function getItemPoints($sku)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		$points_code = Mage::helper('points')->CustomerTypeCode(Mage::getSingleton('customer/session')->getCustomer()->getVipLevel());
		
		return $product->getData($points_code);
	}
	
	public function getTotalQty()
	{
		$total_qty = 0;
		$items = $this->getItems();
		foreach($items as $item){
			if($item->getParentItemId()){
				continue;
			}else{
				$total_qty += $item->getQty();
			}
		
		}
		
		return $total_qty ;
	}
	
	
   public function getTotalPoints()
   {
		$total_points = 0;
		$custmer_for_catalog_code = Mage::helper('points')->CustomerTypeCode(Mage::getSingleton('customer/session')->getCustomer()->getVipLevel());
		$items = $this->getItems();
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
	
	

}