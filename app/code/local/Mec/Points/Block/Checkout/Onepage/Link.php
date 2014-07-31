<?php
class Mec_Points_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
	public function getCheckoutUrl()
    {	
		if(Mage::getStoreConfigFlag('points/general/enable', $store = null)){
			return $this->getUrl('points/checkout', array('_secure'=>true));
		}else{
			return $this->getUrl('checkout/onepage', array('_secure'=>true));
		}
		
        
    }


}
			