<?php
class Magestore_Sociallogin_FbloginController extends Mage_Core_Controller_Front_Action{

    public function loginAction() {            
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$isAuth = $this->getRequest()->getParam('auth');
		$facebook = Mage::getModel('sociallogin/fblogin')->newFacebook();
		$userId = $facebook->getUser();
		
		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied'){
			echo("<script>window.close()</script>");
		}elseif ($isAuth && !$userId){
			$loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
 		$user = Mage::getModel('sociallogin/fblogin')->getFbUser();
 
		if ($isAuth && $user){
			$data =  array('firstname'=>$user['first_name'], 'lastname'=>$user['last_name'], 'email'=>$user['email']);
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email']);
			if(!$customer || !$customer->getId()){
				$customer = Mage::helper('sociallogin')->createCustomer($data);
				if(Mage::getStoreConfig('sociallogin/fblogin/is_send_password_to_customer')){
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
			die("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();</script>");   	}
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