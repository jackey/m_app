<?php   
class Mec_Lilylookbook_Block_Output extends Mage_Core_Block_Template{   

	public function getProduct(){
	
		return $this->getData('product');
	}
	
	
	public function getGalleryDescriptionById($id)
	{
		$resource = Mage::getSingleton('core/resource');
		// $writeConnection = $resource->getConnection('core_write');
		$readConnection = $resource->getConnection('core_read');
	
		$description = $readConnection->fetchOne("SELECT `description` FROM `catalog_product_entity_media_gallery_value` WHERE `value_id` = {$id}");
		
		return $description;
		
	}
	
	


}