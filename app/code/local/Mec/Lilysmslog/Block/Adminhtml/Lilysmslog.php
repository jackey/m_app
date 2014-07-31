<?php


class Mec_Lilysmslog_Block_Adminhtml_Lilysmslog extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_lilysmslog";
	$this->_blockGroup = "lilysmslog";
	$this->_headerText = Mage::helper("lilysmslog")->__("Lilysmslog Manager");
	$this->_addButtonLabel = Mage::helper("lilysmslog")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}