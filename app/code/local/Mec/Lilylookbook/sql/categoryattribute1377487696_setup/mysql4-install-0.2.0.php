<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "is_special_effect",  array(
    "type"     => "int",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Special Effect",
    "input"    => "select",
    "class"    => "special_effect",
    "source"   => "eav/entity_attribute_source_boolean",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    "visible"  => true,
    "required" => true,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 