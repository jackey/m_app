<?php   
class Mec_Points_Block_Shipping extends Mage_Core_Block_Template{   
	
	
	protected $_address;
	protected $_customer;
	
	public function ReadModel()
	{
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		return $readConnection;
	}
	
	public function getShippingProvince()
	{
		$table = "lily_points_province";
		$province_query = "SELECT * FROM {$table}";
		$_province = $this->ReadModel()->fetchAll($province_query);
		
		return $_province;
	}
	
	
	
	public function getCitys()
	{
		$table = "lily_shipping_states";
		$city_query = "SELECT `city` FROM `lily_shipping_states` ";
		$citys = $this->ReadModel()->fetchCol($city_query);
		
		return array_flip(array_flip($citys));
	}
	
	
	
	
	public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }
	
	public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses());
    }
	
	public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
	
	
	public function getQuote()
	{
	
		return  Mage::getModel('checkout/cart')->getQuote();
	}
	
	 public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getBillingAddress();
                if(!$this->_address->getFirstname()) {
                    $this->_address->setFirstname($this->getQuote()->getCustomer()->getFirstname());
                }
                if(!$this->_address->getLastname()) {
                    $this->_address->setLastname($this->getQuote()->getCustomer()->getLastname());
                }
            } else {
                $this->_address = Mage::getModel('sales/quote_address');
            }
        }

        return $this->_address;
    }
	
	public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                // ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }
	
	
	
	
	
}	