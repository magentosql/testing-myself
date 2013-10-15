<?php
class Magestore_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract{
	public function getCustomerByEmail($email){
		return Mage::getModel('customer/customer')->getCollection()
					->addFieldToFilter('email', $email)
					->getFirstItem();
	}
		
	public function createCustomer($data){
		$customer = Mage::getModel('customer/customer')
					->setFirstname($data['firstname'])
					->setLastname($data['lastname'])
					->setEmail($data['email']);
						
		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e){}
        		
		return $customer;
	}
	
	public function getTwConsumerKey(){
		return Mage::getStoreConfig('sociallogin/twlogin/consumer_key');
	}
	
	public function getTwConsumerSecret(){
		return Mage::getStoreConfig('sociallogin/twlogin/consumer_secret');
	}
	
	public function getTwConnectingNotice(){
		return Mage::getStoreConfig('sociallogin/twlogin/connecting_notice');
	}
	
	public function getYaAppId(){
		return Mage::getStoreConfig('sociallogin/yalogin/app_id');
	}
	
	public function getYaConsumerKey(){
		return Mage::getStoreConfig('sociallogin/yalogin/consumer_key');
	}
	
	public function getYaConsumerSecret(){
		return Mage::getStoreConfig('sociallogin/yalogin/consumer_secret');
	}
	
	public function getGoConsumerKey(){
		return Mage::getStoreConfig('sociallogin/gologin/consumer_key');
	}
	
	public function getGoConsumerSecret(){
		return Mage::getStoreConfig('sociallogin/gologin/consumer_secret');
	}
	
	public function getFbAppId(){
		return Mage::getStoreConfig('sociallogin/fblogin/app_id');
	}
	
	public function getFbAppSecret(){
		return Mage::getStoreConfig('sociallogin/fblogin/app_secret');
	}
	
	public function getAuthUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('sociallogin/fblogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}
	
	public function getDirectLoginUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('sociallogin/fblogin/login', array('_secure'=>$isSecure));
	}
	
	public function getLoginUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('customer/account/login', array('_secure'=>$isSecure));
	}
	
	public function getEditUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('customer/account/edit', array('_secure'=>$isSecure));
	}
	
	// by Hai.Ta
	public function getFqAppkey()
	{
		return Mage::getStoreConfig('sociallogin/fqlogin/consumer_key');
	}
	
	public function getFqAppSecret()
	{
		return Mage::getStoreConfig ('sociallogin/fqlogin/consumer_secret');
	}	
	
	public function getLiveAppkey()
	{	
		return Mage::getStoreConfig('sociallogin/livelogin/consumer_key');
	}
	
	public function getLiveAppSecret()
	{
		return Mage::getStoreConfig('sociallogin/livelogin/consumer_secret');
	}
	
	public function getMpConsumerKey(){
		return Mage::getStoreConfig('sociallogin/mplogin/consumer_key');
	}
	
	public function getMpConsumerSecret(){
		return Mage::getStoreConfig('sociallogin/mplogin/consumer_secret');
	}
	
	public function getAuthUrlFq(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('sociallogin/fqlogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}
    
    public function getAuthUrlLive(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('sociallogin/livelogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}    
    
	public function getAuthUrlMp(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('sociallogin/mplogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}
	
	public function getLinkedConsumerKey(){
		return Mage::getStoreConfig('sociallogin/linklogin/app_id');
	}
	
	public function getLinkedConsumerSecret(){
		return Mage::getStoreConfig('sociallogin/linklogin/secret_key');
	}
	
    public function getResponseBody($url)
	{
		if(ini_get('allow_url_fopen') != 1) 
		{
			@ini_set('allow_url_fopen', '1');
		}
		if(ini_get('allow_url_fopen') == 1) 
		{
			$ch = curl_init();
            
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);           
			$contents = curl_exec($ch);            
			curl_close($ch);
			// var_dump($contents);die();
		} else {
		   	$contents=file_get_contents($url);
		}
		
		return $contents;
	}
	    
	// end Hai.Ta
	//by Chun (Hua.Khanh.Trung)
	public function getPerResultStatus($result){
		$result = str_replace( array('"',':'),array( '',','), $result );
		$rs = explode(",", $result);
		return $rs[1];
	}	
	public function getPerEmail($result){
		$result = str_replace( array('"',':'),array( '',','), $result );
		$rs = explode(",", $result);
		if($rs[3]){
			return $rs[3];
		}
		else{
			return "";
		}
	}
	//end Chun
}