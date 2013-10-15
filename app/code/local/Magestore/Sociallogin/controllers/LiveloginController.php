<?php
class Magestore_Sociallogin_LiveloginController extends Mage_Core_Controller_Front_Action{

    public function loginAction(){  
		$isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = Mage::getModel('sociallogin/livelogin')->newLive();        
		try{
			$json = $live->authenticate($code);
			$user = $live->get("me", $live->param);	
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
		}		
        $first_name = $user->first_name;
		$last_name = $user->last_name;
		$email = $user->emails->account;						
		if ($isAuth){
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);		
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email']);
			if(!$customer || !$customer->getId()){
				$customer = Mage::helper('sociallogin')->createCustomer($data);	
				if (Mage::getStoreConfig('sociallogin/livelogin/is_send_password_to_customer')){
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
}