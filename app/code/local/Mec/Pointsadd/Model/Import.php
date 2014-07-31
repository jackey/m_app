<?php

class Mec_Pointsadd_Model_Import extends Mage_Core_Model_Config_Data
{
	 public function _afterSave()
    {
		$this->uploadAndImport($this);
    }
	
	public function uploadAndImport(Varien_Object $object)
	{
		$csvFile = $_FILES['groups']["tmp_name"]["shipping"]["fields"]["shipping_import"]["value"];
		if (!empty($csvFile)) {
			$writeModel = Mage::getSingleton('core/resource')->getConnection('core_write');
			$csv = trim(file_get_contents($csvFile));
			if (!empty($csv)) {
				$clear_query = "truncate table `lily_shipping_states`";
				$writeModel->query($clear_query);
				
				$exceptions = array();
                $csvLines = explode("\n", $csv);
                $csvLine = array_shift($csvLines);
                $csvLine = $this->_getCsvValues($csvLine);
				if (count($csvLine) < 3) {
                    $exceptions[0] = Mage::helper('shipping')->__('Invalid Import State File Format');
                }
				
				
				foreach ($csvLines as $k=>$csvLine) {
					$csvLine = $this->_getCsvValues($csvLine);
					if(count($csvLine) > 0 && count($csvLine) < 3){
						$exceptions[0] = Mage::helper('shipping')->__('Invalid Matrix Rates File Format');
						break;
					}
				}
				
				if(empty($exceptions)){
					$import_count = 0;
					foreach ($csvLines as $k=>$csvLine) {
						$csvLine = $this->_getCsvValues($csvLine);
						Mage::log($csvLine);
						$insert_query = "INSERT INTO `lily_shipping_states`(`id`, `city`, `area`, `store`) VALUES ('', '{$csvLine[0]}', '{$csvLine[1]}', '{$csvLine[2]}')";
						$writeModel->query($insert_query);
						$import_count++;
					}
				
				}
				if (!empty($exceptions)) {
                    throw new Exception( "\n" . implode("\n", $exceptions) );
                }
				
				
				
			}
		
		}
	
	}
	
	private function _getCsvValues($string, $separator=",")
    {
        $elements = explode($separator, trim($string));
        for ($i = 0; $i < count($elements); $i++) {
            $nquotes = substr_count($elements[$i], '"');
            if ($nquotes %2 == 1) {
                for ($j = $i+1; $j < count($elements); $j++) {
                    if (substr_count($elements[$j], '"') > 0) {
                        // Put the quoted string's pieces back together again
                        array_splice($elements, $i, $j-$i+1, implode($separator, array_slice($elements, $i, $j-$i+1)));
                        break;
                    }
                }
            }
            if ($nquotes > 0) {
                // Remove first and last quotes, then merge pairs of quotes
                $qstr =& $elements[$i];
                $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
                $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
                $qstr = str_replace('""', '"', $qstr);
            }
            $elements[$i] = trim($elements[$i]);
        }
        return $elements;
    }
	
}