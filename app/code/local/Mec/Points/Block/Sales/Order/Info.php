<?php
class Mec_Points_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
	 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('points/order/info.phtml');
    }
	


}