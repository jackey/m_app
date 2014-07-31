<?php   
class Mec_Points_Block_Checkout extends Mage_Core_Block_Template{   

	public function getCustomer(){
	
		return Mage::getSingleton('customer/session')->getCustomer(); 
	}
	
	
	public function getUsername(){
		
		return $this->getCustomer()->getFirstname();
	}
	
	
	public function getIdCard()
	{
		return $this->getCustomer()->getIdCard();
	}
	
	public function getTelphone()
	{
		return $this->getCustomer()->getTelephone();
	
	}

}