<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `lily_sms_log` (
 `sms_id` int(11) NOT NULL AUTO_INCREMENT,
 `tel_num` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 `msg` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `date_time` date NOT NULL,
 `status` smallint(2) NOT NULL,
 PRIMARY KEY (`sms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `lily_sms_log` CHANGE `date_time` `date_time` TIMESTAMP NOT NULL ;

SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 