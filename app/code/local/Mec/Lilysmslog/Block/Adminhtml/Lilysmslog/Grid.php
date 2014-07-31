<?php

class Mec_Lilysmslog_Block_Adminhtml_Lilysmslog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("lilysmslogGrid");
				$this->setDefaultSort("sms_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("lilysmslog/lilysmslog")->getCollection();
				Mage::log($collection->getSelect()->__tostring());
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
			$this->addColumn("sms_id", array(
				"header" => Mage::helper("lilysmslog")->__("ID"),
				"align" =>"center",
				"width" => "50px",
				"type" => "number",
				"index" => "sms_id",
			));
			
			
			$this->addColumn("tel_num", array(
				"header" => Mage::helper("lilysmslog")->__("Telephone"),
				"align" =>"center",
				"width" => "50px",
				"type" => "text",
				"index" => "tel_num",
			));
                
			$this->addColumn("msg", array(
				"header" => Mage::helper("lilysmslog")->__("SMS Message"),
				"align" =>"center",
				"width" => "100px",
				"type" => "text",
				"index" => "msg",
			));
            
			$this->addColumn("date_time", array(
				"header" => Mage::helper("lilysmslog")->__("Date Time"),
				"align" =>"center",
				"width" => "50px",
				"type" => "datetime",
				"index" => "date_time",
				'renderer'  => 'Mec_Lilysmslog_Block_Adminhtml_Sms_Renderer_Date'
			));
			
			$this->addColumn("status", array(
				"header" => Mage::helper("lilysmslog")->__("Send Status"),
				"align" =>"center",
				"width" => "50px",
				"type" => "options",
				'options'   => array(
					'0' => Mage::helper("lilysmslog")->__("Succeed"),
					'1' => Mage::helper("lilysmslog")->__("Failure")
				),
				"index" => "status",
			));
			
			
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('sms_id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_lilysmslog', array(
					 'label'=> Mage::helper('lilysmslog')->__('Remove Lilysmslog'),
					 'url'  => $this->getUrl('*/adminhtml_lilysmslog/massRemove'),
					 'confirm' => Mage::helper('lilysmslog')->__('Are you sure?')
				));
			return $this;
		}
			

}