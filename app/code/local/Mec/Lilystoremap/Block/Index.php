<?php   
class Mec_Lilystoremap_Block_Index extends Mage_Core_Block_Template{   
	
	
	
	public function getPoints(){
		if(Mage::getSingleton('core/session')->getSponts() != ""){
			return Mage::getSingleton('core/session')->getSponts();
		}else{
			return '121.523859,31.233777';
		}
	}
	
	
	public function getName(){
		if(Mage::getSingleton('core/session')->getSname() != ""){
			return Mage::getSingleton('core/session')->getSname();
		}else{
			return '上海八佰伴';
		}
	
	}
	
	public function getAllCity()
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$city = $readModel->fetchCol('SELECT `store_area` FROM ' . $table );
		
		return  array_flip(array_flip($city));
		
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
	
	


}