<?php

class Magestore_Sociallogin_YaloginController extends Mage_Core_Controller_Front_Action{
	
	// url to login
    public function loginAction() {
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$yalogin = Mage::getModel('sociallogin/yalogin');
		$hasSession = $yalogin->hasSession();
		if($hasSession == FALSE) {
			$authUrl = $yalogin->getAuthUrl();			
			$this->_redirectUrl($authUrl);
		}else{
			$session = $yalogin->getSession();
			$userSession = $session->getSessionedUser();
			$profile = $userSession->loadProfile();
			$emails = $profile->emails;
			$user = array();
			foreach($emails as $email){
				if($email->primary == 1)
					$user['email'] = $email->handle;
			}
			$user['firstname'] = $profile->givenName;
			$user['lastname'] = $profile->familyName;
			
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($user['email']);
			if(!$customer || !$customer->getId()){
				$customer = Mage::helper('sociallogin')->createCustomer($user);
				if (Mage::getStoreConfig('sociallogin/yalogin/is_send_password_to_customer')){
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
	
	public function testAction(){
		
	}
}