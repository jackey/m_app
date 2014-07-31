<?php
class Mec_Lilystoremap_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getInfoByCity($city)
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
	
		
		$query = "SELECT * FROM {$table} WHERE `store_area` = '{$city}'";
		$results = $readModel->fetchAll($query);
		
		return $results;
	}
	
	
	
	public function getInfoByProvince($province)
	{
		
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
	
		
		$query = "SELECT * FROM {$table} WHERE `province` = '{$province}'";
		$results = $readModel->fetchAll($query);
		
		return $results;
	
	}
	
	
	public function getAllCityByProvince($province)
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$city = $readModel->fetchCol("SELECT `store_area` FROM  {$table} WHERE `province` = '{$province}' " );
		
		return  array_flip(array_flip($city));
	
	}
	
	
	public function getAllProvince()
	{
	
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$province = $readModel->fetchCol('SELECT `province` FROM ' . $table );
		
		return  array_flip(array_flip($province));
	
	}

	
	
	public function getAllCity()
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$city = $readModel->fetchCol('SELECT `store_area` FROM ' . $table );
		
		return  array_flip(array_flip($city));
		
	}
	
}
	 