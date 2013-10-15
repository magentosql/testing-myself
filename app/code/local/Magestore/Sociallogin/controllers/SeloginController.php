<?php

class Magestore_Sociallogin_SeloginController extends Mage_Core_Controller_Front_Action{
	
	
    public function loginAction() {
		$se = Mage::getModel('sociallogin/selogin')->newSe(); 
		$userId = $se->mode; 	
		$coreSession = Mage::getSingleton('core/session');	
		
		if($userId==NULL){
			$se_session = Mage::getModel('sociallogin/selogin')->setSeIdlogin($se);
			$url = $se_session->authUrl();
			$this->_redirectUrl($url);
		}
		 else{                        
            if (!$se->validate()){
				$se_session = Mage::getModel('sociallogin/selogin')->setSeIdlogin($se);
				$url = $se_session->authUrl();
				$this->_redirectUrl($url);
			}
			else{
				$user_info = $se->getAttributes();                 
                if(count($user_info)){
                    $frist_name = $user_info['namePerson/first'];
                    $last_name = $user_info['namePerson/last'];
                    $email = $user_info['contact/email'];
                    if(!$frist_name){
                        if($user_info['namePerson/friendly']){
                        $frist_name = $user_info['namePerson/friendly'] ;   
                        }
                        else{
                            $email = explode("@", $email);
                            $frist_name = $email['0'];
                        }                   
                    }

                    if(!$last_name){
                        $last_name = '_aol';
                    }
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email']);
                    if(!$customer || !$customer->getId()){
						$customer = Mage::helper('sociallogin')->createCustomer($data);				
						if (Mage::getStoreConfig('sociallogin/selogin/is_send_password_to_customer')){
							$customer->sendPasswordReminderEmail();
						}
						 if ($customer->getConfirmation())
						{
							try {
								$customer->setConfirmation(null);
								$customer->save();
							}catch (Exception $e) {
								Mage::getSingleton('core/session')->addError(Mage::helper('sociallogin')->__('Error'));
							}
						}
                    }
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                    die("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();</script>");
                }                
                else{
                   $coreSession->addError($this->__('Login failed as you have not granted access.'));
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
                }
			}
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