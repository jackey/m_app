<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "erp_gift_num", array("type"=>"varchar"));
$installer->addAttribute("order", "gift_store_address", array("type"=>"varchar"));
$installer->addAttribute("order", "erp_sales_num", array("type"=>"varchar"));
$installer->addAttribute("order", "order_place_name", array("type"=>"varchar"));
$installer->addAttribute("order", "order_place_idcard", array("type"=>"varchar"));
$installer->addAttribute("order", "order_place_tel", array("type"=>"varchar"));
$installer->addAttribute("order", "erp_status", array("type"=>"varchar"));
$installer->endSetup();
	  