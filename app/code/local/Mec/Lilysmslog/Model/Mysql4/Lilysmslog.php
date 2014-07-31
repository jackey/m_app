<?php
class Mec_Lilysmslog_Model_Mysql4_Lilysmslog extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("lilysmslog/lilysmslog", "sms_id");
    }
}