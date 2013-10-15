<?php
class Magestore_Sociallogin_FqloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user FoursQuare
	**/
    public function loginAction() {            		
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$isAuth = $this->getRequest()->getParam('auth');
		$foursquare = Mage::getModel('sociallogin/fqlogin')->newFoursquare();
		$code = $_REQUEST['code'];	
		$date = date('Y-m-d');
		$date = str_replace('-', '', $date);
		$oauth = $foursquare->GetToken($code);

		if(!$oauth){
			echo("<script>window.close()</script>");
			return ;
		}
		$url = 'https://api.foursquare.com/v2/users/self?oauth_token='.$oauth.'&v='.$date;
		try{
			$json = Mage::helper('sociallogin')->getResponseBody($url);
		}catch( Exception $e){
			$coreSession = Mage::getSingleton('core/session');
			$coreSession->addError('Login fail!');			
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
		}		
		$string = $foursquare->getResponseFromJsonString($json);		
		$first_name = $string->user->firstName;
		$last_name = $string->user->lastName;
		$email = $string->user->contact->email;						
		if ($isAuth && $oauth){
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email']);
			if(!$customer || !$customer->getId()){
				$customer = Mage::helper('sociallogin')->createCustomer($data);
				if(Mage::getStoreConfig('sociallogin/fqlogin/is_send_password_to_customer')){
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
}