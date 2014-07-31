<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `lily_shipping_states` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `area` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `store` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;





SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 