<?php
class Mec_Points_Block_Sales_Order_History extends Mage_Sales_Block_Order_History
{

	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('points/order/history.phtml');

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
    }

	
	
	public function getOrderItemsImg($order)
	{
		$html = "";
		$items = $order->getAllItems();
		foreach($items as $item){		
			Mage::log($item->getOrderId());
			if($item->getParentItemId()){
				continue;
			}else{	
				$p_id = $item->getProductId();
				$_product = Mage::getModel('catalog/product')->load($p_id);
				$html .= "<img src='" . Mage::helper('catalog/image')->init($_product, 'small_image')->resize(55, 60) . "' width='55' height='60'/>";
			}
		
		}
	
		return $html;
	}
	
	

}