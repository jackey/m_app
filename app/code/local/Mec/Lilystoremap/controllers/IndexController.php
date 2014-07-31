<?php
class Mec_Lilystoremap_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Store Map"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("storemap", array(
                "label" => $this->__("Store Map"),
                "title" => $this->__("Store Map")
		   ));

      $this->renderLayout(); 
	  
    }
	
	
	public function getResultByStorenameAction()
	{
		
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
		 
		 $store_name = $this->getRequest()->getParam('city');
		 
		 // $query = ""
		 
	
	}
	
	
	public function getResultByCityAction(){
	
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
	
		$html = "<ul>";
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
		
		$city = $this->getRequest()->getParam('city');
		$province = $this->getRequest()->getParam('province');
		$city_area = $this->getRequest()->getParam('city_area');
		
		if($city_area == "" && isset($city_area)){
			$query = "SELECT * FROM {$table} WHERE `store_area` = '{$city}' AND `province` = '{$province}'";
		}else if($city == ""){
			$query = "SELECT * FROM {$table} WHERE `province` = '{$province}'";
		}else{
			$query = "SELECT * FROM {$table} WHERE `store_area` = '{$city}' AND `store_range` = '{$city_area}' AND `province` = '{$province}'";
		}
		
		// Mage::log($query);
		
		$results = $readModel->fetchAll($query);
		
		foreach($results as $result){
			if($result['store_point'] != "" && isset($result['store_point'])){
				$html .= "<li class='result-city'".'onclick="' ."getMap({$result['id']})" . '"'  .">";
				$html .= "<div class='title'>" . $result['store_name'] . "</div>";
				$html .= "<div class='address'>" . $result['store_address'] . "</div>";
				$html .= "<div class='tel'>" . $result['store_tel'] . "</div>";
				$html .= "</li>";
			}else{
				$html .= "<li class='result-city'>";
				$html .= "<div class='title'>" . $result['store_name'] . "</div>";
				$html .= "<div class='address'>" . $result['store_address'] . "</div>";
				$html .= "<div class='tel'>" . $result['store_tel'] . "</div>";
				$html .= "</li>";
			}
			
		}
		
		$html .= "</ul>";
		
		$response['success'] = true;
		$response['return_html'] = $html;
		Mage::getSingleton('core/session')->setScity($city);
		$this->getResponse()->setBody(Zend_Json::encode($response));
		
	}
	
	
	public function getCitysByProvinceAction()
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
		 
		$html = "<option value=''>" . $this->__('Please Select One') . "</option>";
		 
		$province = $this->getRequest()->getParam('province'); 
		
		$query = "SELECT `store_area` FROM {$table} WHERE `province` = '{$province}'";
		$results = $readModel->fetchCol($query);
		
		$results = array_flip(array_flip($results));
		
		foreach($results as $result){
			// Mage::log($result);
			$html .= "<option value='" . $result . "'>" . $result . '</option>';
		}
		
		$response['success'] = true;
		$response['return_html'] = $html;
		
		
		Mage::getSingleton('core/session')->setSprovince($province);
		
		$this->getResponse()->setBody(Zend_Json::encode($response));
		
	}
	
	
	public function getRangeByCityAction()
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
	
		$html = "";
		
		$city = $this->getRequest()->getParam('city'); 
		
		$query = "SELECT `store_range` FROM {$table} WHERE `store_area` = '{$city}'";
		$results = $readModel->fetchCol($query);
		
		$results = array_flip(array_flip($results));
		// Mage::log($results);
		if(!empty($results)){
			
			foreach($results as $result){
			// Mage::log($result);
				$html .= "<option value='" . $result . "'>" . $result . '</option>';
			}
			
			$response['has_range'] = true;
			$response['success'] = true;
			$response['return_html'] = $html;
		
		}else{
			$response['has_range'] = false;
		}
		
		
		Mage::getSingleton('core/session')->setScity($city);
		$this->getResponse()->setBody(Zend_Json::encode($response));
		
		
	}
	
	
	
	public function setMapInfoAction()
	{
		$table = 'lily_store';
		$readModel = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$response = array(
            'success' => false,
            'error'=> false,
            'message' => false,
         );
		
		$id = $this->getRequest()->getParam('id');
		$query = "SELECT `province`, `store_area`, `store_point`, `store_name` FROM {$table} WHERE `id` = '{$id}'";
		 
		$result = $readModel->fetchAll($query);
		
		Mage::getSingleton('core/session')->setSponts($result[0]['store_point']);
		Mage::getSingleton('core/session')->setSname($result[0]['store_name']);
		Mage::getSingleton('core/session')->setScity($result[0]['store_area']);
		Mage::getSingleton('core/session')->setSprovince($result[0]['province']);
		
		$response['success'] = true;
		$response['return_url'] = Mage::getUrl('storemap');
		$this->getResponse()->setBody(Zend_Json::encode($response));
		
	}
	
}