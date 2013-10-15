<?php
class Magestore_Sociallogin_Model_Gologin extends Zend_Oauth_Consumer {

	protected $_options = null;
	public function __construct(){
		$this->_config = new Zend_Oauth_Config;		
		$this->_options = array(
			'consumerKey'       => Mage::helper('sociallogin')->getGoConsumerKey(),
			'consumerSecret'    => Mage::helper('sociallogin')->getGoConsumerSecret(),
			'signatureMethod'   => 'HMAC-SHA1',
			'version'           => '1.0',
			'requestTokenUrl'   => 'https://www.google.com/accounts/OAuthGetRequestToken',
			'accessTokenUrl'    => 'https://www.google.com/accounts/OAuthGetAccessToken',
			'authorizeUrl'      => 'https://www.google.com/accounts/OAuthAuthorizeToken'
		);
	
		$this->_config->setOptions($this->_options);
	}
	
	public function setCallbackUrl($url){
		$this->_config->setCallbackUrl($url);
	}
	
	public function getOptions(){
		return $this->_options ;
	}
}
  
