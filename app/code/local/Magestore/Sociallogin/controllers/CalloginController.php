<?php
class Magestore_Sociallogin_CalloginController extends Mage_Core_Controller_Front_Action{
	
	/**
	* getToken and call profile user Clavid
	**/
    public function loginAction($name_blog) {
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$cal = Mage::getModel('sociallogin/callogin')->newCal();       
		$userId = $cal->mode;        
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId){
            $cal_session = Mage::getModel('sociallogin/callogin')->setcalIdlogin($aol, $name_blog);
            $url = $cal_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}
        else{                        
            if (!$cal->validate()){                
               $coreSession->addError('Login failed as you have not granted access.');			
               die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
            }
            else{                
                $user_info = $cal->getAttributes();                 
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
                        $last_name = '_cal';
                    }
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email']);
                    if(!$customer || !$customer->getId()){
						$customer = Mage::helper('sociallogin')->createCustomer($data);				
						if (Mage::getStoreConfig('sociallogin/callogin/is_send_password_to_customer')){
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
                else{
                   $coreSession->addError('Login failed as you have not granted access.');			
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
                }
            }           
        }
    }
    
    public function setBlockAction()
    {             
        /*$template =  $this->getLayout()->createBlock('sociallogin/callogin')
                ->setTemplate('sociallogin/au_cal.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
    
    public function setClaivdNameAction(){
        $data = $this->getRequest()->getPost();
        if($data){
            $name = $data['name'];
            $url = Mage::getModel('sociallogin/callogin')->getCalLoginUrl($name);
            $this->_redirectUrl($url);
        }
        else{
            Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
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