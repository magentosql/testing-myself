<?php
class IDP_AuthorizeCIM_IndexController extends Mage_Core_Controller_Front_Action
{
public function indexAction() {
	if (!Mage::getSingleton('customer/session')->isLoggedIn()){
			 header("Location: /customer/account/login/");
			 exit();
	}
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $customer = Mage::getModel('customer/customer');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/account_dashboard')
        );

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('authorizecim');
        }		

	$this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }

public function removeAction() {
	if (!Mage::getSingleton('customer/session')->isLoggedIn()){
			 header("Location: /customer/account/login/");
			 exit();
	}
		//$addressId = $this->getRequest()->getParam('id', false);
		foreach($this->getRequest()->getParams() as $key=>$value) {
			switch($key){
				case 'cid':{
					$custID=$value;
				}
				case 'cprofile':{
					$cprofile=$value;
				}
		}
		}
			//remove possible approval code
			$fields = preg_split('/-/',$cprofile);
			$CustomerProfileID = $fields[0];
			$PaymentProfileID = $fields[1]; 
			$cprofile=$CustomerProfileID."-".$PaymentProfileID;
		
		$orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); 
		$orderPaymentTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_payment'); 
		
		$sql = "UPDATE $orderPaymentTable p, $orderTable o SET p.cc_ss_start_month=0 WHERE p.po_number LIKE '$cprofile%' AND p.method='authorizecimsoap' AND o.customer_id='$custID' AND o.entity_id=p.parent_id";
		

        Mage::getSingleton('core/session')->addNotice("Card has been removed");
        session_write_close();
		$results = Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
		$this->_redirect('../authorizecim');
	}

public function newAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $customer = Mage::getModel('customer/customer');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('authorizecim');
        }	


	$this->renderLayout();
	
}

public function postAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		 if (!Mage::getSingleton('customer/session')->isLoggedIn()){
				 header("Location: /customer/account/login/");
				 exit();
		}
		$auth = Mage::getModel('authorizeCIM/authorizecimdirect');
		$payment = new Varien_Object();
				foreach($this->getRequest()->getParams() as $key=>$value) {
				switch($key){
					case 'cprofile':{ $payment->setToken($value); }
					case "firstname":{ $payment->setFirst($value); }
					case "lastname":{ $payment->setLast($value); }
					case "company":{ $payment->setCompany($value); }
					case "telephone":{ $payment->setTelephone($value); }
					case "fax":{ $payment->setFax($value); }
					case "street1":{ $payment->setStreet1($value); }
					case "street2":{ $payment->setStreet2($value); }
					case "city":{ $payment->setCity($value); }
					case "region_id":{ $payment->setState($value); }
					case "postcode":{ $payment->setZip($value); }
					case "country_id":{ $payment->setCountryId($value); }
					case "cc_type":{ $payment->setCcType($value); }
					case "cc_number":{ $payment->setCcNumber($value); }
					case "cc_exp_month":{ $payment->setCcExpMonth($value); }
					case "cc_exp_year":{ $payment->setCcExpYear($value); }
					case "cc_cid":{ $payment->setCcCid($value); }
					default:
						//print "<BR>$key : $value";
					}
				}
		$ccnum=$payment->getCcNumber();
		if($ccnum>'') { 
			$auth->updatecc($payment);
			Mage::getSingleton('core/session')->addNotice("Card has been updated");
			$this->_redirect('../authorizecim');

		}

	}
}
