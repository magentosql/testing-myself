<?php
class Magestore_Sociallogin_AlloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user aol
	**/
    public function loginAction() { 
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$aol = Mage::getModel('sociallogin/allogin')->newAol();       
		$userId = $aol->mode;        
		$coreSession = Mage::getSingleton('core/session');		
		if(!$userId){
            $aol_session = Mage::getModel('sociallogin/allogin')->setAolIdlogin($aol);
            $url = $aol_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}
        else{                        
            if (!$aol->validate()){                
               $aol_session = Mage::getModel('sociallogin/allogin')->setAolIdlogin($aol);
                $url = $aol_session->authUrl();
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            }
            else{                
                $user_info = $aol->getAttributes();                 
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
						if (Mage::getStoreConfig('sociallogin/aollogin/is_send_password_to_customer')){
							$customer->sendPasswordReminderEmail();
						}
                    }
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                    die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
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
        $template =  $this->getLayout()->createBlock('sociallogin/aollogin')
                ->setTemplate('sociallogin/au_wp.phtml')->toHtml();
        echo $template;
    }
    
	
    public function setScreenNameAction(){
        $data = $this->getRequest()->getPost();		
		$name = $data['name'];
        if($name){            
            $url = Mage::getModel('sociallogin/allogin')->getAlLoginUrl($name);			
            $this->_redirectUrl($url);
        }
        else{
            Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
        }
    }
}
