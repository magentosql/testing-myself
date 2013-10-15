<?php
/**
Copyright 2009-2012 Eric Levine
IDP
 */
class IDP_AuthorizeCIM_Model_Authorizecimdirect extends Mage_Payment_Model_Method_Cc
{   protected $_code  = 'authorizecimsoap';
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;
    const CC_URL_LIVE = 'https://api.authorize.net/xml/v1/request.api';
	const CC_URL_TEST = 'https://apitest.authorize.net/xml/v1/request.api';
    const STATUS_APPROVED = 'Approved';
	const STATUS_SUCCESS = 'Complete';
	const PAYMENT_ACTION_AUTH_CAPTURE = 'authorize_capture';
	const PAYMENT_ACTION_AUTH = 'authorize';
    const STATUS_COMPLETED    = 'Completed';
    const STATUS_DENIED       = 'Denied';
    const STATUS_FAILED       = 'Failed';
    const STATUS_REFUNDED     = 'Refunded';
    const STATUS_VOIDED       = 'Voided';
	public function getGatewayUrl() {
		if(Mage::getStoreConfig('payment/authorizecimsoap/test'))
		{
			return Mage::getStoreConfig('payment/authorizecimsoap/cgi_url_test');
		}
		else
		{
			return Mage::getStoreConfig('payment/authorizecimsoap/cgi_url');
		}
	}
	public function getUsername($storeId=0) {
		if(Mage::getStoreConfig('payment/authorizecimsoap/test'))
		{
			//return 'xxxxxxxxx';   //hard code test ID username here
			return Mage::getStoreConfig('payment/authorizecimsoap/testusername',$storeId);
		}
		else
		{
			//return 'xxxxxxxxx';   //hard code live ID username here
			return Mage::getStoreConfig('payment/authorizecimsoap/username',$storeId);
		}
	}
	public function getPassword($storeId=0) {
		if(Mage::getStoreConfig('payment/authorizecimsoap/test'))
		{
			//return 'xxxxxxxxxxxxxxxxxxx';  //hard code test ID key/password herekey/password here
			return Mage::getStoreConfig('payment/authorizecimsoap/testpassword',$storeId);
		}
		else
		{
			//return 'xxxxxxxxxxxxxxxxxxx';  //hard code live ID key/password here
			return Mage::getStoreConfig('payment/authorizecimsoap/password',$storeId);
		}
	}
	// public function getDebug() {
				// return true;
	// }
	public function getDebug() {
			if((Mage::getStoreConfig('payment/authorizecimsoap/test')) & (file_exists(Mage::getBaseDir() . '/var/log'))) {
				return Mage::getStoreConfig('payment/authorizecimsoap/debug'); 
			} else {
				return false;
			}
	}
	public function getTest() {
		return Mage::getStoreConfig('payment/authorizecimsoap/test');
	}
	public function getLogPath() {
		return Mage::getBaseDir() . '/var/log/authorizecimsoap.log';
	}
	public function getLiveUrl() {
		return Mage::getStoreConfig('payment/authorizecimsoap/cgi_url');
	}
	public function getTestUrl() {
		return Mage::getStoreConfig('payment/authorizecimsoap/cgi_url_test');
	}
	public function getPaymentAction()
	{
		return Mage::getStoreConfig('payment/authorizecimsoap/payment_action');
	}
	public function getStrictCVV() {
		return true;
	}
	public function createCustomerXML($ccnum, $CustomerEmail, $ExpirationDate, $custID, $billToWho, $cvv, $storeId=0) {
		$doc = new SimpleXMLElement('<?xml version ="1.0" encoding = "utf-8"?><createCustomerProfileRequest/>');
		$doc->addAttribute('xmlns','AnetApi/xml/v1/schema/AnetApiSchema.xsd');
		$merchantAuth = $doc->addChild('merchantAuthentication');
		$merchantAuth->addChild('name', htmlentities( $this->getUsername($storeId) ));
		$merchantAuth->addChild('transactionKey', htmlentities( $this->getPassword($storeId) ));
		$profile = $doc->addChild('profile');
		$profile->addChild('merchantCustomerId', $custID);
		$profile->addChild('email', $CustomerEmail);
		$PaymentProfile = $profile->addChild('paymentProfiles');
		$billTo = $PaymentProfile->addChild('billTo');
		$billTo->addChild('firstName',$billToWho['firstname']);
		$billTo->addChild('lastName',$billToWho['lastname']);
		if(!empty($billToWho['company'])){
			$billTo->addChild('company',htmlentities($billToWho['company']));
        } else { $billTo->addChild('company',"xxxx"); }
		$billTo->addChild('address',$billToWho['address']);
		$billTo->addChild('city',$billToWho['city']);
		$billTo->addChild('state',$billToWho['region']);
		$billTo->addChild('zip',$billToWho['postcode']);
		$billTo->addChild('country',$billToWho['country_id']);
		$billTo->addChild('phoneNumber',$billToWho['telephone']);
		$billTo->addChild('faxNumber',$billToWho['fax']);
		$Payment1 = $PaymentProfile->addChild('payment');
		$cc = $Payment1->addChild('creditCard');
		$cc->addChild('cardNumber', htmlentities($ccnum));
		$cc->addChild('expirationDate', $ExpirationDate);
		if($cvv) {$cc->addChild('cardCode', $cvv); }
		$CustProfileXML = $doc->asXML();
		return $CustProfileXML;
	}
	public function addPaymentProfileXML($CustProfile, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId=0) {
		// Build the XML request 
		$doc = new SimpleXMLElement('<?xml version = "1.0" encoding = "utf-8"?><createCustomerPaymentProfileRequest/>');
		$doc->addAttribute('xmlns','AnetApi/xml/v1/schema/AnetApiSchema.xsd');
		$merchantAuth = $doc->addChild('merchantAuthentication');
		$merchantAuth->addChild('name', htmlentities( $this->getUsername($storeId) ));
		$merchantAuth->addChild('transactionKey', htmlentities( $this->getPassword($storeId) ));
		$profile = $doc->addChild('customerProfileId', $CustProfile);
		$paymentProfile = $doc->addChild('paymentProfile');
		$billTo = $paymentProfile->addChild('billTo');
		$billTo->addChild('firstName',$billToWho['firstname']);
		$billTo->addChild('lastName',$billToWho['lastname']);
		if(!empty($billToWho['company'])){
			$billTo->addChild('company',htmlentities($billToWho['company']));
        } else { $billTo->addChild('company',"xxxx"); }
		$billTo->addChild('address',$billToWho['address']);
		$billTo->addChild('city',$billToWho['city']);
		$billTo->addChild('state',$billToWho['region']);
		$billTo->addChild('zip',$billToWho['postcode']);
		$billTo->addChild('country',$billToWho['country_id']);
		$billTo->addChild('phoneNumber',$billToWho['telephone']);
		$billTo->addChild('faxNumber',$billToWho['fax']);
		$payment = $paymentProfile->addChild('payment');
		$credit = $payment->addChild('creditCard');
		$credit->addChild('cardNumber', $ccnum);
		$credit->addChild('expirationDate', $ExpirationDate);
		if($cvv) {$credit->addChild('cardCode', $cvv); }
		$CustPaymentXML = $doc->asXML();
		return $CustPaymentXML;
	}
	public function updatePaymentProfileXML($CustProfile, $PayProfile, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId=0) {
		// Build the XML request 
		$doc = new SimpleXMLElement('<?xml version = "1.0" encoding = "utf-8"?><updateCustomerPaymentProfileRequest/>');
		$doc->addAttribute('xmlns','AnetApi/xml/v1/schema/AnetApiSchema.xsd');
		$merchantAuth = $doc->addChild('merchantAuthentication');
		$merchantAuth->addChild('name', htmlentities( $this->getUsername($storeId) ));
		$merchantAuth->addChild('transactionKey', htmlentities( $this->getPassword($storeId) ));
		$profile = $doc->addChild('customerProfileId', $CustProfile);
		$paymentProfile = $doc->addChild('paymentProfile');
		$billTo = $paymentProfile->addChild('billTo');
		$billTo->addChild('firstName',$billToWho['firstname']);
		$billTo->addChild('lastName',$billToWho['lastname']);
		if(!empty($billToWho['company'])){
			$billTo->addChild('company',htmlentities($billToWho['company']));
        } else { $billTo->addChild('company',"xxxx"); }
		$billTo->addChild('address',$billToWho['address']);
		$billTo->addChild('city',$billToWho['city']);
		$billTo->addChild('state',$billToWho['region']);
		$billTo->addChild('zip',$billToWho['postcode']);
		$billTo->addChild('country',$billToWho['country_id']);
		$billTo->addChild('phoneNumber',$billToWho['telephone']);
		$billTo->addChild('faxNumber',$billToWho['fax']);
		$payment = $paymentProfile->addChild('payment');
		$credit = $payment->addChild('creditCard');
		$credit->addChild('cardNumber', $ccnum);
		$credit->addChild('expirationDate', $ExpirationDate);
		if($cvv) {$credit->addChild('cardCode', $cvv); }
		$pay2 = $paymentProfile->addChild('customerPaymentProfileId', $PayProfile);
		$CustPaymentXML = $doc->asXML();
		return $CustPaymentXML;
	}
	public function createCustomerShippingAddressXML($CustProfile, $shipToWho, $storeId=0) {
		// Build the XML request 
		$doc = new SimpleXMLElement('<?xml version = "1.0" encoding = "utf-8"?><createCustomerShippingAddressRequest/>');
		$doc->addAttribute('xmlns','AnetApi/xml/v1/schema/AnetApiSchema.xsd');
		$merchantAuth = $doc->addChild('merchantAuthentication');
		$merchantAuth->addChild('name', htmlentities( $this->getUsername($storeId) ));
		$merchantAuth->addChild('transactionKey', htmlentities( $this->getPassword($storeId) ));
		$profile = $doc->addChild('customerProfileId', $CustProfile);
		$shipTo = $doc->addChild('address');
		$shipTo->addChild('firstName',$shipToWho['firstname']);
		$shipTo->addChild('lastName',$shipToWho['lastname']);
		if(!empty($shipToWho['company'])){
			$shipTo->addChild('company',htmlentities($shipToWho['company']));
        } else { $shipTo->addChild('company',"xxxx"); }
		$billTo->addChild('address',$shipToWho['address']);
		$shipTo->addChild('city',$shipToWho['city']);
		$shipTo->addChild('state',$shipToWho['region']);
		$shipTo->addChild('zip',$shipToWho['postcode']);
		$shipTo->addChild('country',$shipToWho['country_id']);
		$shipTo->addChild('phoneNumber',$shipToWho['telephone']);
		$shipTo->addChild('faxNumber',$shipToWho['fax']);
		$CustShipXML = $doc->asXML();
		return $CustShipXML;
	}
	public function getProfileXML($CustProfile, $storeId=0) {
		// Build the XML request 
		$doc = new SimpleXMLElement('<?xml version = "1.0" encoding = "utf-8"?><getCustomerProfileRequest/>');
		$doc->addAttribute('xmlns','AnetApi/xml/v1/schema/AnetApiSchema.xsd');
		
		$merchantAuth = $doc->addChild('merchantAuthentication');
		$merchantAuth->addChild('name', htmlentities( $this->getUsername($storeId) ));
		$merchantAuth->addChild('transactionKey', htmlentities( $this->getPassword($storeId) ));
		
		$profile = $doc->addChild('customerProfileId', $CustProfile);
		$getCustProfileXML = $doc->asXML();
		
		return $getCustProfileXML;
	}
	public function createTransXML($amount, $tax, $CustomerProfileID, $PaymentProfileID, $callby, $invoiceno, $authtransID, $Approval, $storeId=0) {
		// Build Transaction Request
		$TxRq = new SimpleXMLElement('<?xml version = "1.0" encoding = "utf-8"?><createCustomerProfileTransactionRequest/>');
		$TxRq->addAttribute('xmlns','AnetApi/xml/v1/schema/AnetApiSchema.xsd');
		$merchantAuth = $TxRq->addChild('merchantAuthentication');
		$merchantAuth->addChild('name', htmlentities( $this->getUsername($storeId) ));
		$merchantAuth->addChild('transactionKey', htmlentities( $this->getPassword($storeId) ));
		$transaction = $TxRq->addChild('transaction');
		if($this->getPaymentAction()=='authorize')
		{
			switch($callby) {
				case 'captureonly':
					$credit = $transaction->addChild('profileTransCaptureOnly');
					$credit->addChild('amount', $amount);
					break;
				case 'capture':
					$credit = $transaction->addChild('profileTransPriorAuthCapture');
					$credit->addChild('amount', $amount);
					break;
				case 'refund':
					$credit = $transaction->addChild('profileTransRefund');
					$credit->addChild('amount', $amount);
					break;
				case 'void':
					$credit = $transaction->addChild('profileTransVoid');
					break;
				default:
					$credit = $transaction->addChild('profileTransAuthOnly');
					$credit->addChild('amount', $amount);
					if($tax>0) {
						$credittax = $credit->addChild('tax');
						$credittax->addChild('amount', $tax);
					}
					break;
			}
		} else {
			switch($callby) {
				case 'refund':
					$credit = $transaction->addChild('profileTransRefund');
					$credit->addChild('amount', $amount);
					break;
				case 'void':
					$credit = $transaction->addChild('profileTransVoid');
					break;
				default:
					$credit = $transaction->addChild('profileTransAuthCapture');
					$credit->addChild('amount', $amount);
					if($tax>0) {
						$credittax = $credit->addChild('tax');
						$credittax->addChild('amount', $tax);
					}
					break;
			}
		}
		$credit->addChild('customerProfileId', $CustomerProfileID);
		$credit->addChild('customerPaymentProfileId', $PaymentProfileID);
		if($this->getPaymentAction()=='authorize')
		{
			switch($callby) {
				case 'captureonly':
					$credit->addChild('approvalCode', $Approval);
					break;
				case 'capture':
					$credit->addChild('transId', $authtransID);
					break;
				case 'refund':
					$credit->addChild('transId', $authtransID);
					break;
				case 'void':
					$credit->addChild('transId', $authtransID);
					break;
				default:
					$order = $credit->addChild('order');
					$order->addChild('invoiceNumber', $invoiceno);
					break;
			}
		} else {
			switch($callby) {
				case 'refund':
					$credit->addChild('transId', $authtransID);
					break;
				case 'void':
					$credit->addChild('transId', $authtransID);
					break;
				default:
					$order = $credit->addChild('order');
					$order->addChild('invoiceNumber', $invoiceno);
					break;
			}
		}
		$TxRqXML = $TxRq->asXML();
		return $TxRqXML;
	}
	public function processRequest($url, $xml)	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    $response = curl_exec($ch);
	    curl_close ($ch);
	      		
	 if($this->getDebug()) {
	      	$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
	      	$logger->info("XML Sent: $xml");
	      	$logger->info("XML Received: $response");
	   }
	      		
	    return $response;
	}
		
	public function parseXML($start, $end, $xml)	 {
		return preg_replace('|^.*?'.$start.'(.*?)'.$end.'.*?$|i', '$1', substr($xml, 335));
	}
	public function parseMultiXML($xml, $ccnum)	 {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("MultiParseXML...");
		}	
		$ccnum='XXXX'.substr($ccnum, -4, 4);		
		$pos = strpos($xml,"<?xml version=");
		$fixedXML=substr($xml,$pos);
		$fixedXML = preg_replace('/xmlns="(.+?)"/', '', $fixedXML);
		$dom=new DOMDocument;
		$dom->loadXML($fixedXML);
		//print_r($dom);
		$profileID=array();
		$CardNo=array();
		$ProfileIDs=$dom->getElementsByTagname('customerPaymentProfileId');
		$index=0;
		foreach ($ProfileIDs as $Profile) {
			$value=$Profile->nodeValue;
			$profileID[]=$value;
			$index++;
		}
		//print_r($profileID);
		$Cards=$dom->getElementsByTagname('cardNumber');
		foreach ($Cards as $Card) {
			$value=$Card->nodeValue;
			$CardNo[]=$value;
		}
		//print_r($CardNo);
		for ($iindex=0; $iindex<$index; $iindex++) {
			if($this->getDebug())
			{
				$logger->info(" Payment: " . $profileID[$iindex]);
				$logger->info(" Card Number: " . $CardNo[$iindex]);
			}	
			if($CardNo[$iindex]==$ccnum) {
				$returnvalue=$profileID[$iindex];
				break;
			}
				$returnvalue=$profileID[0];
		}
			return $returnvalue;
	}
	public function validate() {
    	$info = $this->getInfoInstance();
		$ccNumber = $info->getCcNumber();
		
		if($this->getDebug())
		{
	    	$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("entering validate()");
		}
		
		/* If the string contains "tkn" assume that the number came from the admin site. */
		if(strpos($ccNumber,'tkn') === FALSE) {	
		
	        //parent::validate();
	        $paymentInfo = $this->getInfoInstance();
	        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
	            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
	        } else {
	            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
	        }
	        return $this;
		}
		return $this;
    }
	public function authorize(Varien_Object $payment, $amount) {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("entering authorize()");
		}
		$this->setAmount($amount)
		->setPayment($payment);

		//if (!$payment->getCcNumber()) { $payment->setCcNumber($payment->getToken()); }
		
		$orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); $orderTransTable=Mage::getSingleton('core/resource')->getTableName('sales_payment_transaction'); 
		$orderno = $payment->getOrder()->getIncrementId();
		$sql = "DELETE p.* FROM $orderTransTable p, $orderTable q WHERE q.increment_id ='".$orderno."' AND q.entity_id=p.order_id AND p.txn_type='authorization';";
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query($sql);

		$result = $this->_call($payment, 'authorize', $amount);
		if($this->getDebug()) { $logger->info(var_export($result, TRUE)); }
		if($result === false)
		{
			$e = $this->getError();
			if (isset($e['message'])) {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment.') . $e['message'];
			} else {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment. Please try later or contact us for help.');
			}
			Mage::throwException($message);
		}
		else
		{
			// Check if there is a gateway error
			if ($result['Status']['statusCode'] == "Ok")
			{
				// Check if there is an error processing the credit card
				if($result['Status']['code'] == "I00001")
				{
					$payment->setStatus(self::STATUS_APPROVED);
					$payment->setTransactionId($result['Status']['transno']);
					$payment->setIsTransactionClosed(0);
					$payment->setTxnId($result['Status']['transno']);
					$payment->setParentTxnId($result['Status']['transno']);
					$payment->setCcTransId($result['Status']['transno']);					
					}
			}
			else
			{
				Mage::throwException("Gateway error code " . $result['Status']['code'] . ": " . $result['Status']['statusDescription']);
			}
		}
	return $this;
	}
public function capture(Varien_Object $payment, $amount) {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("entering capture()");
		}
		$this->setAmount($amount)
			->setPayment($payment);
	$orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); $orderInvoiceTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice'); 
	$orderno = $payment->getOrder()->getIncrementId();
	//$this->getInvoice()->getId();  invoice number
	$sql = "SELECT * FROM $orderInvoiceTable p, $orderTable q WHERE q.increment_id ='".$orderno."' AND q.entity_id=p.order_id AND p.increment_id>'' ORDER BY p.entity_id desc LIMIT 1;";
	$data2 = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($sql);
	$lastinvoice = $data2['increment_id'];
		if($this->getDebug()) { $logger->info("lastinvoice: $lastinvoice"); }
		if(!$lastinvoice) {
			$result = $this->_call($payment,'capture', $amount);
		} else { 
			$result = $this->_call($payment,'captureonly', $amount);
		}
		if($this->getDebug()) { $logger->info(var_export($result, TRUE)); }
		if($result['Status']['transno']=='0') {
			$result = $this->_call($payment,'captureonly', $amount);
			if($this->getDebug()) { $logger->info(var_export($result, TRUE)); }
		}
		if($result === false)
		{
			$e = $this->getError();
			if (isset($e['message'])) {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment.') . $e['message'];
			} else {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment. Please try later or contact us for help.');
			}
			Mage::throwException($message);
		}
		else
		{
			// Check if there is a gateway error
			if ($result['Status']['statusCode'] == "Ok")
			{
				// Check if there is an error processing the credit card
				if($result['Status']['code'] == "I00001")
				{
					//$payment->setIsTransactionClosed(1);
					//$payment->registerCaptureNotification();
					$payment->setIsTransactionClosed(0);
					$payment->setTransactionId($result['Status']['transno']);
					$payment->setLastTransId($result['Status']['transno'])
							->setCcApproval($result['Status']['code'])
							->setCcTransId($result['Status']['transno'])
							->setCcAvsStatus($result['Status']['statusCode'])
							->setCcCidStatus($result['Status']['statusCode']);
				}
			}
			else
			{
				Mage::throwException("Gateway error code " . $result['Status']['code'] . ": " . $result['Status']['statusDescription']);
			}
		}
		return $this;
	}
public function refund(Varien_Object $payment, $amount) {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("entering refund()");
		}
		$this->setAmount($amount)
			->setPayment($payment);
			
		$result = $this->_call($payment,'refund', $amount);
		if($this->getDebug()) { $logger->info(var_export($result, TRUE)); }
		if($result === false)
		{
			$e = $this->getError();
			if (isset($e['message'])) {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment.') . $e['message'];
			} else {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment. Please try later or contact us for help.');
			}
			Mage::throwException($message);
		}
		else
		{
			// Check if there is a gateway error
			if ($result['Status']['statusCode'] == "Ok")
			{
				// Check if there is an error processing the credit card
				if($result['Status']['code'] == "I00001")
				{
					//$payment->registerRefundNotification();
					$payment->setCcApproval($result['Status']['code'])
						->setTransactionId($result['Status']['transno'])
						->setCcTransId($result['Status']['transno'])
						->setCcAvsStatus($result['Status']['statusCode'])
						->setCcCidStatus($result['Status']['statusCode']);
				}
			}
			else
			{
				Mage::throwException("Gateway error code " . $result['Status']['code'] . ": " . $result['Status']['statusDescription']);
			}
		}
		return $this;
	}
public function void(Varien_Object $payment) {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("entering void()");
		}
		//$this->setAmount($amount)
		//	->setPayment($payment);
		$result = $this->_call($payment,'void', 99999);
		if($this->getDebug()) { $logger->info(var_export($result, TRUE)); }
		if($result === false)
		{
			$e = $this->getError();
			if (isset($e['message'])) {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment.') . $e['message'];
			} else {
				$message = Mage::helper('authorizeCIM')->__('There has been an error processing your payment. Please try later or contact us for help.');
			}
			Mage::throwException($message);
		}
		else
		{
			// Check if there is a gateway error
			if ($result['Status']['statusCode'] == "Ok")
			{
				// Check if there is an error processing the credit card
				if($result['Status']['code'] == "I00001")
				{
					$cancelorder=true;
					if ($cancelorder) { $payment->getOrder()->setState('canceled', true, 'Canceled/Voided');
					} else { $payment->registerVoidNotification(); }
					$payment->setCcApproval($result['Status']['code'])
						->setTransactionId($result['Status']['transno'])
						->setCcTransId($result['Status']['transno'])
						->setCcAvsStatus($result['Status']['statusCode'])
						->setCcCidStatus($result['Status']['statusCode']);
					$payment->setStatus(self::STATUS_VOIDED);
				}
			}
			else
			{
				Mage::throwException("Gateway error code " . $result['Status']['code'] . ": " . $result['Status']['statusDescription']);
			}
		}
		return $this;
	}
protected function _call(Varien_Object $payment,$callby='', $amountcalled) {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("paymentAction: ".$this->getPaymentAction());
			$storeId = $payment->getOrder()->getStoreId();
			$logger->info("Storeid: ".$storeId);
		}
		//print "<pre>"; print_r($payment); print "</pre>"; exit;
		$ExpirationDate = $payment->getCcExpYear() .'-'. str_pad($payment->getCcExpMonth(), 2, '0', STR_PAD_LEFT);
		$invoiceno = $payment->getOrder()->getIncrementId();
		$CustomerEmail = $payment->getOrder()->getCustomerEmail();
		$custID = $payment->getOrder()->getCustomerId();
		$storeId = $payment->getOrder()->getStoreId();

		$billToWho = $payment->getOrder()->getBillingAddress();
		$billToWho['address']=preg_replace('/[^A-Za-z0-9_]/', ' ', $billToWho->getStreet1()." ".$billToWho->getStreet2());	
		
		$shipToWho = $payment->getOrder()->getShippingAddress();
		$shipToWho['address']=preg_replace('/[^A-Za-z0-9_]/', ' ', $shipToWho->getStreet1()." ".$shipToWho->getStreet2());	

		$tax = $payment->getOrder()->getTaxAmount();
		$cvv = $payment->getCcCid();
		if($this->getStrictCVV()) { if(!$cvv) {$cvv="111"; } }
		///$extra1 = Mage::helper('core/http')->getRemoteAddr(true);
		//$extra2 = $_SERVER['REMOTE_ADDR'];
		//$extra="x_customer_ip=".$extra2;
		$ccnum = $payment->getCcNumber();
		$ponum = $payment->getPoNumber();
		if($ccnum=='') { $ccnum="tkn-$ponum";	}
		if($ponum=='') { $ponum=$ccnum;	}
		$cim=$payment->getCcSsStartMonth();

		if($this->getDebug()) { $logger->info("CcNumber PoNumber: $ccnum, $ponum SaveCimCC: $cim\n"); }
		if ($amountcalled<1) { $amountcalled = $this->getAmount(); }
		$url = $this->getGatewayUrl();
		if(strpos($ccnum,'tkn') !== FALSE) {
			$fields = preg_split('/-/',$ccnum);
			$CustomerProfileID = $fields[1];
			$PaymentProfileID = $fields[2];
			$fields2 = preg_split('/-/',$ponum);
			$Approval = $fields2[2];				
			$fullcarddata=true;
		} else {
			$fields = preg_split('/-/',$ponum);
			$CustomerProfileID = $fields[0];
			$fullcarddata=true;
			if((isset($fields[1])) and ($callby!='authorize') and (strpos($ccnum,'-') !== FALSE)) { 
				$PaymentProfileID = $fields[1]; 
			} else {
				$PaymentProfileID = 0; 
			}
			if(isset($fields[2])) { 
				$Approval = $fields[2];
			} else {
				$Approval = 0; 
			}
		}
		$authtransID = $payment->getOrder()->getTransactionId();
		if($authtransID<1) { $authtransID=$payment->getParentTransactionId();	}
		if($authtransID<1) { $authtransID=$payment->getCcTransId();	}
		$authtrans2 = preg_split ('/-/',$authtransID);
		$authtransID = $authtrans2[0];
		if($this->getDebug()) { $logger->info("from database: $CustomerProfileID, $PaymentProfileID, $authtransID\n"); }
		/* If we have the Customer ID and Payment ID, we can just do the transaction */
		if(($CustomerProfileID>0) and ($PaymentProfileID>0)) {
			$TxRqXML = $this->createTransXML($amountcalled, $tax, $CustomerProfileID, $PaymentProfileID, $callby, $invoiceno, $authtransID, $Approval, $storeId);
			$TxRqResponse = $this->processRequest($url, $TxRqXML);
			if(isset($shipToWho['lastname']) and $shipToWho['lastname']>'') {
				$createCustomerShippingAddressXML=$this->createCustomerShippingAddressXML($CustomerProfileID, $shipToWho, $storeId);
				$response = $this->processRequest($url, $createCustomerShippingAddressXML);
				 if($this->getDebug()) {		$logger->info("\n\n Shipping Address Response: $response\n\n"); }
			}
		}
		else {
			/* First try to create a Customer Profile */
			$CustProfileXML = $this->createCustomerXML($ccnum, $CustomerEmail, $ExpirationDate, $custID, $billToWho, $cvv, $storeId);
			$response = $this->processRequest($url, $CustProfileXML);
			$resultErrorCode = $this->parseXML('<code>','</code>',$response);
			/* Get Customer Profile ID */
			$CustomerProfileID = (int) $this->parseXML('<customerProfileId>','</customerProfileId>', $response);
			/* Get Payment Profile ID */
			$PaymentProfileID = (int) $this->parseXML('<customerPaymentProfileIdList><numericString>','</numericString></customerPaymentProfileIdList>', $response);
			$ExistingCustProfile=$CustomerProfileID;
			$resultText = $this->parseXML('<text>','</text>',$response);
			$resultCode = $this->parseXML('<resultCode>','</resultCode>',$response);
			if($resultErrorCode == 'E00039') {
					 if($this->getDebug()) {		$logger->info("\n\n ALREADY HAVE A CUST PROFILE \n\n"); }
				$split = preg_split('/ /',$resultText);
				$ExistingCustProfile = $split[5];
				$CustomerProfileID = $ExistingCustProfile;
				$addPaymentProfileXML = $this->addPaymentProfileXML($ExistingCustProfile, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId);
				$response = $this->processRequest($url, $addPaymentProfileXML);
				$PaymentProfileID = (int) $this->parseXML('<customerPaymentProfileId>','</customerPaymentProfileId>', $response);
				$resultErrorCode = $this->parseXML('<code>','</code>',$response);
				 if($resultErrorCode == 'E00039') { // Using an existing card already
				 if($this->getDebug()) {		$logger->info("\n\n ALREADY HAVE A PAYMENT PROFILE WITH THE CARD \n\n"); }
					//Get Correct PaymentProfileID
					$getCustXML = $this->getProfileXML($ExistingCustProfile, $storeId);
					$responseGET = $this->processRequest($url, $getCustXML);
					$PaymentProfileID = $this->parseMultiXML($responseGET, $ccnum);
					if($fullcarddata) {
						$updatePaymentProfileXML = $this->updatePaymentProfileXML($ExistingCustProfile, $PaymentProfileID, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId);
						$response = $this->processRequest($url, $updatePaymentProfileXML);
						 if($this->getDebug()) {		$logger->info("\n\n UPDATED PROFILE $PaymentProfileID \n\n"); }
					}
					if($this->getDebug()) {		$logger->info("\n\ngetCustXML: $getCustXML ...\n...\nresponseGET$responseGET \n\n"); }
				}
				 if($PaymentProfileID == '0') { // Using an existing card already
				 if($this->getDebug()) {		$logger->info("\n\n PROFILE ERROR \n\n"); }
					//Get Correct PaymentProfileID
					$getCustXML = $this->getProfileXML($ExistingCustProfile, $storeId);
					$responseGET = $this->processRequest($url, $getCustXML);
					$PaymentProfileID = $this->parseMultiXML($responseGET, $ccnum);
					if($this->getDebug()) {		$logger->info("\n\ngetCustXML: $getCustXML ...\n...\nresponseGET$responseGET \n\n"); }
				}				
			 if($this->getDebug()) { $logger->info("\nUSING $CustomerProfileID - $PaymentProfileID"); }
			}

			if(isset($shipToWho['lastname']) and $shipToWho['lastname']>'') {
				$createCustomerShippingAddressXML=$this->createCustomerShippingAddressXML($ExistingCustProfile, $shipToWho, $storeId);
				$response = $this->processRequest($url, $createCustomerShippingAddressXML);
				 if($this->getDebug()) {		$logger->info("\n\n Shipping Address Response: $response\n\n"); }
			}
			
			$TxRqXML = $this->createTransXML($amountcalled, $tax, $CustomerProfileID, $PaymentProfileID, $callby, $invoiceno, $authtransID, $Approval, $storeId);
			$TxRqResponse = $this->processRequest($url, $TxRqXML);
		}
		$resultText = $this->parseXML('<text>','</text>',$TxRqResponse);
		$resultCode = $this->parseXML('<resultCode>','</resultCode>',$TxRqResponse);
		$resultErrorCode = $this->parseXML('<code>','</code>',$TxRqResponse);
		$transauthidar = $this->parseXML('<directResponse>','</directResponse>',$TxRqResponse);
	 	$fieldsAU = preg_split('/,/',$transauthidar);
		$responsecode  = $fieldsAU[0];
		if(!$responsecode=="1") { $resultCode="No";  }
		if(isset($fieldsAU[4])) { 
					$approval = $fieldsAU[4];
				} else {
					$approval = 0; 
				}
		if (strlen($approval)<6) { $approval=$Approval; }
		if(isset($fieldsAU[6])) { 
					$transno = $fieldsAU[6];
				} else {
					$transno = 0; 
				}
		if($this->getDebug()) { $logger->info("TransID = $transno \n"); }
		if($this->getDebug()) { $logger->info("Approval Code = $approval \n"); }
		$paymentInfo = $this->getInfoInstance();
		if (($CustomerProfileID>'0') AND ($PaymentProfileID>'0')) {
			$token="$CustomerProfileID-$PaymentProfileID-$approval"; 
			$paymentInfo->setCybersourceToken($token);
			$paymentInfo->setPoNumber($token);
			$paymentInfo->getOrder()->setTransactionId();
			if($paymentInfo->getCcSsStartMonth()=="on") {	
				$paymentInfo->setCcSsStartMonth('1'); 
			} else {
				if($paymentInfo->getCcSsStartMonth()!="1") {	
					$paymentInfo->setCcSsStartMonth('0'); }
		}}
		$result['Status']['transno'] = $transno;
		$result['Status']['approval'] = $approval;
		$result['Status']['CustomerProfileID'] = $CustomerProfileID;
		$result['Status']['PaymentProfileID'] = $PaymentProfileID;
		$result['Status']['statusCode'] = $resultCode;
		$result['Status']['code'] = $resultErrorCode;
		$result['Status']['statusDescription'] = $resultText;
		if($this->getDebug()) { $logger->info("STATUS CODE = $resultErrorCode - $resultCode - $resultText"); }
		return $result;
	}
protected function _call2(Varien_Object $payment,$callby='') {
		if($this->getDebug())
		{
			$writer = new Zend_Log_Writer_Stream($this->getLogPath());
			$logger = new Zend_Log($writer);
			$logger->info("callby: ".$callby);
		}

		//print "<pre>"; print_r($payment); print "</pre>"; exit;
		$billToWho = array();
		$billToWho['firstname']=$payment->getFirst();
		$billToWho['lastname']=$payment->getLast();
		$billToWho['company']=$payment->getCompany();
		$billToWho['telephone']=$payment->getTelephone();
		$billToWho['fax']=$payment->getFax();
		$billToWho['address']=preg_replace('/[^A-Za-z0-9_]/', ' ', $payment->getStreet1()." ".$payment->getStreet2());	
		$billToWho['city']=$payment->getCity();
		$billToWho['region']=$payment->getState();
		$billToWho['postcode']=$payment->getZip();
		$billToWho['country_id']=$payment->getCountryId();
		//print "<pre>payment: "; print_r($payment); print "</pre>"; 
		//print "<pre>billtowho:"; print_r($billToWho); print "</pre>"; 
		$ExpirationDate = $payment->getCcExpYear() .'-'. str_pad($payment->getCcExpMonth(), 2, '0', STR_PAD_LEFT);
		//$invoiceno = $payment->getOrder()->getIncrementId();
		$custID=Mage::getSingleton('customer/session')->getCustomerId();
		$customer = Mage::getModel('customer/customer')->load($custID)->getData();
		$CustomerEmail = $customer["email"];
		$storeId = $customer["store_id"];
		//$billToWho = $payment->getOrder()->getBillingAddress();
		$cvv = $payment->getCcCid();
		$ccnum = $payment->getCcNumber();
		$ponum=$ccnum;
		$payment->setCcSsStartMonth('1');
		$token=$payment->getToken();

		if($token>'') {
			$fields = preg_split('/-/',$token);
			$CustomerProfileID = $fields[0];
			$PaymentProfileID = $fields[1];
			$fullcarddata = true;
		} else {
			$CustomerProfileID = 0;
			$PaymentProfileID = 0;
			$fullcarddata = false;
		}
		$cim=$payment->getCcSsStartMonth();
		
		if($this->getDebug()) { 
		$logger->info("Token: $token $CustomerProfileID $PaymentProfileID\n");
		$logger->info("CcNumber PoNumber: $ccnum, $ponum SaveCimCC: $cim\n"); }
		$url = $this->getGatewayUrl();

		$authtransID = '';
		if($this->getDebug()) { $logger->info("from database: $CustomerProfileID, $PaymentProfileID, $authtransID\n"); }
		/* If we have the Customer ID and Payment ID, we can just do the transaction */
		if(($CustomerProfileID>0) and ($PaymentProfileID>0) AND ($fullcarddata)) {
				$updatePaymentProfileXML = $this->updatePaymentProfileXML($CustomerProfileID, $PaymentProfileID, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId);
//				$response = $this->processRequest($url, $updatePaymentProfileXML);
				 if($this->getDebug()) {		$logger->info("\n\n UPDATED PROFILE $PaymentProfileID \n\n"); }

//			$TxRqXML = $this->createTransXML($amountcalled, $tax, $CustomerProfileID, $PaymentProfileID, $callby, $invoiceno, $authtransID, $Approval, $storeId);
			$TxRqResponse = $this->processRequest($url, $updatePaymentProfileXML);

		}
		else {
		
			/* First try to create a Customer Profile */
			if($CustomerProfileID<1) {
				$CustProfileXML = $this->createCustomerXML($ccnum, $CustomerEmail, $ExpirationDate, $custID, $billToWho, $cvv, $storeId);
				$response = $this->processRequest($url, $CustProfileXML);
				$resultErrorCode = $this->parseXML('<code>','</code>',$response);
				/* Get Customer Profile ID */
				$CustomerProfileID = (int) $this->parseXML('<customerProfileId>','</customerProfileId>', $response);
			}
			/* Get Payment Profile ID */
			$PaymentProfileID = (int) $this->parseXML('<customerPaymentProfileIdList><numericString>','</numericString></customerPaymentProfileIdList>', $response);
			$ExistingCustProfile=$CustomerProfileID;
			$resultText = $this->parseXML('<text>','</text>',$response);
			$resultCode = $this->parseXML('<resultCode>','</resultCode>',$response);
			if($resultErrorCode == 'E00039') {
					 if($this->getDebug()) {		$logger->info("\n\n ALREADY HAVE A CUST PROFILE \n\n"); }
				$split = preg_split('/ /',$resultText);
				$ExistingCustProfile = $split[5];
				$CustomerProfileID = $ExistingCustProfile;
				$addPaymentProfileXML = $this->addPaymentProfileXML($ExistingCustProfile, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId);
				$response = $this->processRequest($url, $addPaymentProfileXML);
				$PaymentProfileID = (int) $this->parseXML('<customerPaymentProfileId>','</customerPaymentProfileId>', $response);
				$resultErrorCode = $this->parseXML('<code>','</code>',$response);
				 if($resultErrorCode == 'E00039') { // Using an existing card already
				 if($this->getDebug()) {		$logger->info("\n\n ALREADY HAVE A PAYMENT PROFILE WITH THE CARD \n\n"); }
					//Get Correct PaymentProfileID
					$getCustXML = $this->getProfileXML($ExistingCustProfile, $storeId);
					$responseGET = $this->processRequest($url, $getCustXML);
					$PaymentProfileID = $this->parseMultiXML($responseGET, $ccnum);
					if($fullcarddata) {
						$updatePaymentProfileXML = $this->updatePaymentProfileXML($ExistingCustProfile, $PaymentProfileID, $ccnum, $ExpirationDate, $billToWho, $cvv, $storeId);
						$response = $this->processRequest($url, $updatePaymentProfileXML);
						 if($this->getDebug()) {		$logger->info("\n\n UPDATED PROFILE $PaymentProfileID \n\n"); }
					}
					if($this->getDebug()) {		$logger->info("\n\ngetCustXML: $getCustXML ...\n...\nresponseGET$responseGET \n\n"); }
				}
				 if($PaymentProfileID == '0') { // Using an existing card already
				 if($this->getDebug()) {		$logger->info("\n\n PROFILE ERROR \n\n"); }
					//Get Correct PaymentProfileID
					$getCustXML = $this->getProfileXML($ExistingCustProfile, $storeId);
					$responseGET = $this->processRequest($url, $getCustXML);
					$PaymentProfileID = $this->parseMultiXML($responseGET, $ccnum);
					if($this->getDebug()) {		$logger->info("\n\ngetCustXML: $getCustXML ...\n...\nresponseGET$responseGET \n\n"); }
				}				
			 if($this->getDebug()) { $logger->info("\nUSING $CustomerProfileID - $PaymentProfileID"); }
			}

		}

		if (($CustomerProfileID>'0') AND ($PaymentProfileID>'0')) {
		$token="$CustomerProfileID-$PaymentProfileID";
		if($this->getDebug()) { $logger->info("\ntoken $CustomerProfileID - $PaymentProfileID"); }
		$orderTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); 
		$orderPaymentTable=Mage::getSingleton('core/resource')->getTableName('sales_flat_order_payment'); 
	
		$first=$billToWho['firstname'];
		$last=$billToWho['lastname'];
	
		$sql = "INSERT INTO $orderTable (state, status, store_id, customer_id, customer_email, customer_firstname, customer_lastname) VALUES ('data','data', $storeId, $custID, '$CustomerEmail', '$first', '$last')";
		
//print "<BR>1: ";
//print $sql;
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query($sql);
	
//	$sql = "SELECT * FROM $orderPaymentTable p, $orderTable q WHERE q.customer_id=".$custID." AND q.entity_id=p.parent_id AND po_number LIKE '".$token."%' AND cc_type > '' ORDER BY p.entity_id desc Limit 1;";

	$sql = "SELECT * FROM $orderTable q WHERE q.customer_id=".$custID." ORDER BY q.entity_id desc Limit 1;";

//print "<BR>2: ";
//print $sql;
	
	$results = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
	foreach($results as $data2) {
		$parent_id = $data2['entity_id'];
	}

		$last4 = substr($payment->getCcNumber(), -4, 4);;
		$_ccType = $payment->getCcType();
		$_ccExpMonth = $payment->getCcExpMonth();
		$_ccExpYear = $payment->getCcExpYear();
	
	
		$sql = "INSERT INTO $orderPaymentTable (parent_id, cc_type, cc_last4, po_number, cc_exp_year, cc_exp_month, cc_ss_start_month, method) VALUES ('$parent_id', '$_ccType', '$last4', '$token', '$_ccExpYear', '$_ccExpMonth', '1', 'authorizecimsoap')";
//print "<BR>3: ";
//print $sql;
			
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query($sql);
		
			//$paymentInfo->setPoNumber($token);
			//$paymentInfo->getOrder()->setTransactionId();
			//$paymentInfo->setCcSsStartMonth('1');  
}

		return true;
	}

	public function updatecc(Varien_Object $payment,$callby='') {

		$result = $this->_call2($payment,'updatecc');		
			//print "<PRE>paymenttest5:<BR>";
			//print_r($payment);
			//print "</PRE>";
	}
}