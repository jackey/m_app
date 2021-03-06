<?php
class Mec_Points_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);        
        $url =  $this->getUrl("points/synchronization/syn");

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setType('button')
                        ->setClass('scalable')
                        ->setLabel(Mage::helper('customer')->__('Synchronization ERP'))
                        ->setOnClick("return conformation ();")
                        ->toHtml();

        $html .="<p class='note'>";
        $html .="<span style='color:#E02525;'>";
        $html .= Mage::helper('customer')->__('This action is synchronization with Erp Gift Stock.');
        $html .="</span>";
        $html .="</p>";


        $html .= "<script  type='text/javascript'>
                            function conformation (){
                                if(confirm('".Mage::helper('customer')->__('Are you sure to synchronization?')."')){
                                    setLocation('$url');
                                }
                            }
                       </script>";

        return $html;
    }

}