<?php
class Magestore_Sociallogin_GologinController extends Mage_Core_Controller_Front_Action{
	
	public function loginAction() {
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		if (!$this->getAuthorizedToken()) {
			$token = $this->getAuthorization();
		}
		else {
			$token = $this->getAuthorizedToken();
		}
		
        return $token;
    }
	
	public function userAction() {
		$gologin = Mage::getModel('sociallogin/gologin');
		$oauth_data = array(
                'oauth_token' => $this->getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier')
         );
		
		$requestToken = Mage::getSingleton('core/session')->getRequestToken();
		// Fixed by Hai Ta
		try{
			$accessToken = $gologin->getAccessToken($oauth_data, unserialize($requestToken));
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
		}
        // end fixed		
		$oauthOptions = $gologin->getOptions();
		
		$httpClient = $accessToken->getHttpClient($oauthOptions);
		$client = new Zend_Gdata($httpClient);

		$feed = $client->getFeed('https://www.google.com/m8/feeds/contacts/default/full');
		$userInfo = $feed->getDom();
		$user = array();
		
		$tempName =  $userInfo->getElementsByTagName("name");
		$name = $tempName->item(0)->nodeValue;//full name
		$arrName = explode(' ', $name, 2);
		$user['firstname'] = $arrName[0];
		$user['lastname'] = $arrName[1];
		
		$tempEmail = $userInfo->getElementsByTagName("email");
		$email = $tempEmail->item(0)->nodeValue;
		$user['email'] = $email;
		
		$customer = Mage::helper('sociallogin')->getCustomerByEmail($user['email']);
		if(!$customer || !$customer->getId()){
			$customer = Mage::helper('sociallogin')->createCustomer($user);
			if (Mage::getStoreConfig('sociallogin/gologin/is_send_password_to_customer')){
				$customer->sendPasswordReminderEmail();
			}
		}
			// fix confirmation
			if ($customer->getConfirmation())
			{
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		die("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();</script>");
		//$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
    }
	
	// if exit access token
	public function getAuthorizedToken() {
        $token = false;
        if (!is_null(Mage::getSingleton('core/session')->getAccessToken())) {
            $token = unserialize(Mage::getSingleton('core/session')->getAccessToken());
        }
        return $token;
    }
     
	// if not exit access token
    public function getAuthorization() {
       	$scope = 'https://www.google.com/m8/feeds/';
		
		$gologin = Mage::getModel('sociallogin/gologin');
        $gologin->setCallbackUrl(Mage::getUrl('sociallogin/gologin/user'));
		       
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier')
            );
            $token = $gologin->getAccessToken($oauth_data, unserialize(Mage::getSingleton('core/session')->getRequestToken()));
            Mage::getSingleton('core/session')->setAccessToken(serialize($token));
            $gologin->redirect();
        } else {
            $token = $gologin->getRequestToken(array('scope' => $scope));
            Mage::getSingleton('core/session')->setRequestToken(serialize($token));
            $gologin->redirect();
        }
        return $token;
    }
	protected function _loginPostRedirect()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
		
        return $session->getBeforeAuthUrl(true);
    }
}