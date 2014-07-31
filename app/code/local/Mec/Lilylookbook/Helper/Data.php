<?php
class Mec_Lilylookbook_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function _getLilyLayoutCode()
	{
		return Mage::getStoreConfig('lily_custom_layout/general/layout_code');		
	}

	
	
	public function outPutBlock($level, $product)
	{
		$block = Mage::getSingleton('core/layout')
            ->createBlock("lilylookbook/output", "cate.".$level , array('product' => $product))
            ->setTemplate("lilylookbook/output/part". $level. ".phtml");
		return $block;
	}
	
}
	 