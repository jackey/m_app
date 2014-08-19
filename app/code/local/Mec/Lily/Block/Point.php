<?php   
class Mec_Lily_Block_Point extends Mage_Core_Block_Template{   


	public function getPointsData()
	{
		$data_array = array(
			'has_card' => false,
			'erp_response' => false,
		);
		
		$card = Mage::getSingleton('customer/session')->getCustomer()->getVipCard();
		// Mage::log($card);
		if($card != "" || $card != 0){
			// Mage::log('In If');
			$data_array['has_card'] = true;
			$id_result = Mage::helper('lily')->QueryIdByVipCard($card);
			if($id_result != "") {
				$id_result = json_decode($id_result);

				
				if($id_result[0]->code == 0) {
					$v_id = $id_result[0]->rows;
					$v_id = $v_id[0][0];
					$points_result = Mage::helper('lily')->QueryPointsById($v_id);
					
					if($points_result != ""){
						$points_result = json_decode($points_result);
						
						if($points_result[0]->code == 0){
							$data_array['points_data'] = $points_result[0]->rows;
							$data_array['erp_response'] = true;
						}
					}
				}
			}
		}
		
		
		return $data_array;
	
		
	}

	
	
	
	
}