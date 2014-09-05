<?php
define('SCRIPT_ROOT',  dirname(__FILE__).'/');
// require_once SCRIPT_ROOT.'include/Client.php';
require_once(Mage::getModuleDir('helper', 'Mec_Lilysms').'/Helper/include/Client.php');
class Mec_Lilysms_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected function IsEnbale()
	{
		return Mage::getStoreConfigFlag('lily_sms/general/enable', $store = null);
	
	}

	 protected function ApiUrl()
    {
        return trim(Mage::getStoreConfig('lily_sms/general/api_url', $store = null));
    }

	 protected function ApiUserName()
    {
        return trim(Mage::getStoreConfig('lily_sms/general/api_user', $store = null));
    }
	
	 protected function ApiKey()
    {
        return trim(Mage::getStoreConfig('lily_sms/general/api_key', $store = null));
    }
	
	
	
	public function SendToSms($phone, $content){
		//Start Post
		$this->sms_send($phone, $content);
		
		
		
		
	}
	
	function sms_send($phone, $content)
	{
		$Message_api = $this->ApiUrl();
		$Message_username = $this->ApiUserName();
		$Message_password = $this->ApiKey();
		
		$connectTimeOut = 10;
		$readTimeOut = 20;
		$proxyhost = false;
		$proxyport = false;
		$proxyusername = false;
		$proxypassword = false;
		
		
		$_content = '【Lily商务时装】亲爱的会员，您领礼品的验证码为：';
		$content = $_content . $content . '，请接店铺通知后凭验证码到店领取，验证码是您领礼品的唯一凭证，请勿泄露';
	
		$client = new Client($Message_api,$Message_username,$Message_password,$Message_password,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
		$statusCode_login = $client->login();
		Mage::log($statusCode_login, null, 'newsms.log');
		$client->setOutgoingEncoding("UTF-8");
		$statusCode = $client->sendSMS(array($phone), $content);
		Mage::log($content);
		Mage::log($statusCode, null, 'newsms.log');
		if($statusCode == 0){
			Mage::getModel('lilysmslog/lilysmslog')->setData(array(
				'tel_num' => $phone,
				'msg' => $content,
				'status' => 0
			))->save();
		
		}else{
			Mage::getModel('lilysmslog/lilysmslog')->setData(array(
				'tel_num' => $phone,
				'msg' => $content,
				'status' => 1
			))->save();
		}
		
		return $result;
	}
	
	
	/*
	
	function sms_send($phone, $content) {
		
		
		//Start Post
		
		$_content = '亲爱的会员，您领礼品的验证码为：';
		
		$Message_api = $this->ApiUrl();
		$Message_username = $this->ApiUserName();
		$Message_password = $this->ApiKey();
		$ch = curl_init();
		$curlPost = 'method=sendsms&' .
		'username=' . $Message_username . 
		'&password=' . $Message_password .
		'&mobile=' . $phone .
		'&msg=' . $_content . $content . '请接店铺通知后凭验证码到店领礼品，验证码是您领礼品的唯一凭证，请勿泄露！【Lily女装】';
		
	
		Mage::log($curlPost);
		// Mage::log($curl_url);
		curl_setopt($ch, CURLOPT_URL, $Message_api);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$curl_result = curl_exec($ch);
		$result = simplexml_load_string($curl_result);
		curl_close($ch);
		if($result->error[0] == 0){
			Mage::getModel('lilysmslog/lilysmslog')->setData(array(
				'tel_num' => $phone,
				'msg' => $content,
				'status' => 0
			))->save();
		
		}else{
			Mage::getModel('lilysmslog/lilysmslog')->setData(array(
				'tel_num' => $phone,
				'msg' => $content,
				'status' => 1
			))->save();
		}
		
		return $result;
	}
	*/

	
	
}
	 