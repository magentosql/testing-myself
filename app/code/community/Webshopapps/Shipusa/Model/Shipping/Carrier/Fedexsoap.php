<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* UsaShipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright  Copyright (c) 2011 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Shipusa_Model_Shipping_Carrier_Fedexsoap
    extends Webshopapps_Shipusa_Model_Shipping_Carrier_Fedex
{
	protected $_numBoxes = 1;

    protected $_code = 'fedexsoap';

    protected $_applyHandlingPackage = FALSE;
    
	public function isTrackingAvailable()
    {
        return false;
    }

 	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
    	if (!$this->getConfigFlag('active')) {
    		return false;
        }

        $this->getConfigData('handling_action') != 'O' ? $this->_applyHandlingPackage = TRUE : 0;

        $this->setRequest($request);

        $this->_result = $this->_getQuotes();

        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }



    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setAccount($account);

        if ($request->getFedexSoapKey()) {
            $key = $request->getFedexSoapKey();
        } else {
            $key = $this->getConfigData('key');
        }
        $r->setKey($key);


        if ($request->getFedexPassword()) {
            $password = $request->getFedexPassword();
        } else {
            $password = $this->getConfigData('fedex_password');
        }
        $r->setPassword($password);

        if ($request->getFedexMeterNumber()) {
            $meterNo = $request->getFedexMeterNumber();
        } else {
            $meterNo = $this->getConfigData('meter_no');
        }
        $r->setMeterNumber($meterNo);

        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
        }
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));
        }

        if ($this->_request->getUpsDestType()) {
        	if ($this->_request->getUpsDestType() == "RES") {
        		$r->setDestType(1);
        	} else {
        		$r->setDestType(0);
        	}
        } else {
        	$r->setDestType($this->getConfigData('residence_delivery'));
        }


        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $r->setDestPostal(trim($request->getDestPostcode()));
        } else {

        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight()!= $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackagePhysicalValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $r->setIgnoreFreeItems(false);
    	$r->setMaxPackageWeight($this->getConfigData('max_package_weight'));

        $this->_rawRequest = $r;

        return $this;
    }

    
    protected function _formRateRequest($purpose)
    {
    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
    		return Mage_Usa_Model_Shipping_Carrier_Fedex::_formRateRequest($purpose);
    	}
        $r = $this->_rawRequest;
			
		$date = date('c');
		
		if(!$this->getConfigData('saturday_pickup')){
			if(date('w')==6){
				$date = date('c', time() + 172800); //adds 2 days if it's a Saturday.
				if ($this->getDebugFlag()) {
        			Mage::helper('wsacommon/log')->postMinor('usashipping','Date modified to ',$date);
				}
			} else if (date('w')==0){
				$date = date('c', time() + 86400); //adds 1 day if it's a Sunday.
				if ($this->getDebugFlag()) {
        			Mage::helper('wsacommon/log')->postMinor('usashipping','Date modified to ',$date);
				}
			}
		}
		
		$client = $this->_createRateSoapClient();
		
      	if (!Mage::helper('wsacommon')->checkItems('c2hpcHBpbmcvc2hpcHVzYS9zaGlwX29uY2U=',
			'aWdsb29tZQ==','c2hpcHBpbmcvc2hpcHVzYS9zZXJpYWw=')) { 
      		Mage::log('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIERpbWVuc2lvbmFsIFNoaXBwaW5n');
      		return Mage::getModel('shipping/rate_result'); 
		}
   
	
		$fedReq['Version'] = $this->getVersionInfo();
		$displayTransitTime = $this->getConfigData('display_transit_time');
		if ($displayTransitTime) {
			$fedReq['ReturnTransitAndCommit'] = true; 
		} else {
			$fedReq['ReturnTransitAndCommit'] = false; 
		}
		
		$fedReq['RequestedShipment']['DropoffType'] = $r->getDropoffType(); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$fedReq['RequestedShipment']['ShipTimestamp'] = $date;
		$fedReq['RequestedShipment']['ServiceType'] = $r->getService(); // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...

		$fedReq['RequestedShipment']['PackagingType'] = $r->getPackaging(); // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		if ($this->getConfigFlag('monetary_value')) {
			$fedReq['RequestedShipment']['TotalInsuredValue']=array('Amount'=>$r->getValue(),'Currency'=>'USD');
		}
		$fedReq['RequestedShipment']['Shipper'] =  array(
				'Address' => array(
					//'StreetLines' => array('Address Line 1'),
		           // 'City' => 'Los Angeles',
		          //  'StateOrProvinceCode' => 'CA',
		            'PostalCode' => $r->getOrigPostal(),
		            'CountryCode' => $r->getOrigCountry(),
		            'Residential' => $r->getDestType())
				);
		$fedReq['RequestedShipment']['Recipient'] = array(
				'Address' => array(
					//'StreetLines' => array('Address Line 1'),
		          //  'City' => 'Richmond',
		          //  'StateOrProvinceCode' => 'BC',
		            'PostalCode' => $r->getDestPostal(),
		            'CountryCode' => $r->getDestCountry(),
		            'Residential' => $r->getDestType())
				);
	//	$fedReq['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
	//	                                                        'Payor' => array('AccountNumber' => '510087704',
	//	                                                                     'CountryCode' => 'US'));
	
		
		$fedReq['RequestedShipment']['RateRequestTypes'] = $this->getConfigData('request_type');
		$fedReq['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY


        $boxes=Mage::getSingleton('shipusa/dimcalculate')->getBoxes($this->_request->getAllItems(),$r->getMaxPackageWeight(),$r->getIgnoreFreeItems());
    
		//$fedReq['RequestedShipment']['PackageCount'] = count($boxes); Can't be caluclated here. May be modified below by split shipments.
       	$this->_numBoxes=count($boxes);
       	
       	$splitIndPackage = $this->getConfigData('break_multiples');
       	$splitMaxWeight = $this->getConfigData('max_multiple_weight');
       	$maxPackageWeight = $r->getMaxPackageWeight();
		
       	$handProdFee=0;       	
		foreach ($boxes as $box) {
	        $billableWeight =  $box['weight'];
	        $dimDetails = array();
	       	$dimDetails['GroupPackageCount']=1;
			if ($purpose == self::RATE_REQUEST_GENERAL && $this->getConfigFlag('monetary_value') ) {
        		$dimDetails['InsuredValue'] = array (
        			'Currency' => 'USD',
        	    	'Amount' => $box['price'],
        		);
        	}
	        if ($splitIndPackage && is_numeric($splitMaxWeight) && $splitMaxWeight> $maxPackageWeight &&
	        	$billableWeight<$splitMaxWeight ) {
	        	for ($remainingWeight=$billableWeight;$remainingWeight>0;) {
	        		if ($remainingWeight-$maxPackageWeight<0) {
	        			$billableWeight=$remainingWeight;
	        			$remainingWeight=0;
	        		} else {
	        			$billableWeight=$maxPackageWeight;
	        			$remainingWeight-=$maxPackageWeight;
	        		}
	        		
	        		if ($this->getDebugFlag()) {
	        			Mage::helper('wsacommon/log')->postMinor('usashipping','Adjusting Box Weight for FedEx Original Weight/New Weight',$box['weight'].' / '.$billableWeight);
	        		}
	        		$dimDetails['Weight'] = array(
        					'Value' => $billableWeight,
        			    	'Units' => 'LB');
	        		if ($box['length']!=0 && $box['width'] != 0 && $box['height'] != 0) {
	        			$dimDetails['Dimensions'] = array(
        		            'Length' => $box['length'],
        			        'Width' => $box['width'],
        			        'Height' => $box['height'],
        			        'Units' =>  'IN');
	        		}
	        		$fedReq['RequestedShipment']['RequestedPackageLineItems'][] = $dimDetails;

	        		if(!$this->_applyHandlingPackage && $box['handling_fee'] > $handProdFee){ 
	        			$handProdFee=$box['handling_fee']; 
	        		} else if ($this->_applyHandlingPackage) {
	        			$handProdFee+=$box['handling_fee'];
	        		}
	        	}
	        } else {
	        	$dimDetails['Weight'] = array(
        					'Value' => $box['weight'],
        			    	'Units' => 'LB');	        
	        	if ($box['length']!=0 && $box['width'] != 0 && $box['height'] != 0) {
        			$dimDetails['Dimensions'] = array(
        	            'Length' => $box['length'],
        		        'Width' => $box['width'],
        		        'Height' => $box['height'],
        		        'Units' =>  'IN');
        		}
	        	$fedReq['RequestedShipment']['RequestedPackageLineItems'][] = $dimDetails;
		        
	        	if(!$this->_applyHandlingPackage && $box['handling_fee'] > $handProdFee){ 
        			$handProdFee=$box['handling_fee']; 
        		} else if ($this->_applyHandlingPackage) {
        			$handProdFee+=$box['handling_fee'];
        		}			
	        }			
		}
		
    	if ($purpose == self::RATE_REQUEST_SMARTPOST) {
            $fedReq['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $fedReq['RequestedShipment']['SmartPostDetail'] = array(
                'Indicia' => ((float)$r->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid')
            );
        }
		
		$fedReq['RequestedShipment']['PackageCount'] = count($fedReq['RequestedShipment']['RequestedPackageLineItems']);
		
		$this->getDebugFlag() ? Mage::helper('wsacommon/log')->postMinor('usashipping','Overall HandlingProduct Fee Applied', $handProdFee) : false;
		
		
	  	$fedReq['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => $r->getKey(), 'Password' =>  $r->getPassword()));
        $fedReq['ClientDetail'] = array('AccountNumber' => $r->getAccount(), 'MeterNumber' =>  $r->getMeterNumber());   
        
        $this->_handlingProductFee = $handProdFee;
                
        return $fedReq;
        
	}


	protected function _parseDimXmlResponse($response)
    {
		$displayTransitTime = $this->getConfigData('display_transit_time');
    	$costArr = array();
        $priceArr = array();
        $timeArr = array();
        
       	//if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR' && $response -> HighestSeverity != 'WARNING')
       	if (is_object($response) && isset($response -> RateReplyDetails) && $response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
       	{

            $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

            $rateReplyDetails =$response -> RateReplyDetails ;
            
            if (count($response -> RateReplyDetails)>1) {
            	foreach ($rateReplyDetails as $entry) {
                   if (in_array((string)$entry->ServiceType, $allowedMethods)) {
                   		if (!is_array($entry->RatedShipmentDetails)) {
                       		$totalNetCharge = (string)$entry->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount;
                   		} else {
                   			$totalNetCharge = (string)$entry->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
                   			
                   		}
                   	
                   		$costArr[(string)$entry->ServiceType] = $totalNetCharge;
                   	
                       	$priceArr[(string)$entry->ServiceType] = $this->getMethodPrice($totalNetCharge, (string)$entry->ServiceType);
                   }
            	   if($displayTransitTime && isset($entry->TransitTime)) {
					 	$timeArr[(string)$entry->ServiceType] = $entry->TransitTime;
					 }
					 if($displayTransitTime && isset($entry->DeliveryTimestamp)) {
					 	$timeArr[(string)$entry->ServiceType] = $entry->DeliveryTimestamp;
					 }
               	}
            } else {
            	// single reply on freemethodquote
            	$entry=$rateReplyDetails;
           		if (in_array((string)$entry->ServiceType, $allowedMethods)) {
           			
               		if (!is_array($entry->RatedShipmentDetails)) {
                       	$totalNetCharge = (string)$entry->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount;
                   	} else {
                   		$totalNetCharge = (string)$entry->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
                   		
                   	}
                   
                   	$costArr[(string)$entry->ServiceType] = $totalNetCharge;
                   
                   	$priceArr[(string)$entry->ServiceType] = $this->getMethodPrice($totalNetCharge, (string)$entry->ServiceType);
                
              	}
             	if($displayTransitTime && isset($entry->TransitTime)) {
				 	$timeArr[(string)$entry->ServiceType] = $entry->TransitTime;
				 }
				 if($displayTransitTime && isset($entry->DeliveryTimestamp)) {
				 	$timeArr[(string)$entry->ServiceType] = $entry->DeliveryTimestamp;
				 }
            }

               asort($priceArr);
        } else {
            $errorTitle = 'error retrieving rates';
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            Mage::helper('wsacommon/log')->postCritical('usashipping','Fedex No rates found','');
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                if ($this->getConfigFlag('home_ground') && $this->getCode('method', $method) == 'Home Delivery') {
                	$rate->setMethodTitle('Ground');
                }
                else {
                	$rate->setMethodTitle($this->getCode('method', $method));	
                }
                if ($displayTransitTime && $timeArr[$method]!= '') {
					$rate->setShip($timeArr[$method]);
                }
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price+$this->handlingProductFee);
                $result->append($rate);
            }
        }
        return $result;
    }
    


     /*****************************************************************
     * COMMON CODE - If change here change in UPS
     */


    protected function _setFreeMethodRequest($freeMethod)
    {
    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
    		return Mage_Usa_Model_Shipping_Carrier_Fedex::_setFreeMethodRequest($freeMethod);
    	}
    	parent::_setFreeMethodRequest($freeMethod);
    	$this->_rawRequest->setIgnoreFreeItems(true);


    }

    public function getTotalNumOfBoxes($weight)
    {
    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
    		return Mage_Usa_Model_Shipping_Carrier_Fedex::getTotalNumOfBoxes($weight);
    	}
        $this->_numBoxes = 1; // now set up with dimensional weights
        $weight = $this->convertWeightToLbs($weight);
        return $weight;
    }

       public function getFinalPriceWithHandlingFee($cost){

    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
    		return parent::getFinalPriceWithHandlingFee($cost);
    	}
    	$handlingFee = $this->getConfigData('handling_fee');
    	if (!is_numeric($handlingFee) || $handlingFee<=0) {
    		return $cost;
    	}

    	$finalMethodPrice = 0;
        $handlingType = $this->getConfigData('handling_type');
        if (!$handlingType) {
            $handlingType = self::HANDLING_TYPE_FIXED;
        }
        $handlingAction = $this->getConfigData('handling_action');
        if (!$handlingAction) {
            $handlingAction = self::HANDLING_ACTION_PERORDER;
        }

        if($handlingAction == self::HANDLING_ACTION_PERPACKAGE)
        {
            if ($handlingType == self::HANDLING_TYPE_PERCENT) {
                $finalMethodPrice = $cost + (($cost * $handlingFee/100) * $this->_numBoxes);
            } else {
                $finalMethodPrice = $cost + ($handlingFee * $this->_numBoxes);
            }
        } else {
            if ($handlingType == self::HANDLING_TYPE_PERCENT) {
                $finalMethodPrice = $cost + ($cost * $handlingFee/100);
            } else {
                $finalMethodPrice = $cost + $handlingFee;
        }

        }
        if ($this->getDebugFlag()) {
        	Mage::helper('wsacommon/log')->postNotice('usashipping','Inbuilt Fedex Handling Fee',$finalMethodPrice-$cost);
        }
        return $finalMethodPrice;
    }



	/**
     * Processing additional validation to check is carrier applicable.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error|boolean
     */
    public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
    		return Mage_Usa_Model_Shipping_Carrier_Fedex::proccessAdditionalValidation($request);
    	}
        //Skip by item validation if there is no items in request
        if(!count($request->getAllItems())) {
            return $this;
        }

      //  $maxAllowedWeight = (float) $this->getConfigData('max_package_weight');
        $errorMsg = '';
        $configErrorMsg = $this->getConfigData('specificerrmsg');
        $defaultErrorMsg = Mage::helper('shipping')->__('The shipping module is not available.');
        $showMethod = $this->getConfigData('showmethod');

      /*  foreach ($request->getAllItems() as $item) {
            if ($item->getProduct() && $item->getProduct()->getId()) {
                if ($item->getProduct()->getWeight() > $maxAllowedWeight) {
                    $errorMsg = ($configErrorMsg) ? $configErrorMsg : $defaultErrorMsg;
                    break;
                }
            }
        } */

        if (!$errorMsg && !$request->getDestPostcode() && $this->isZipCodeRequired($request->getDestCountryId())) {
            $errorMsg = Mage::helper('shipping')->__('This shipping method is not available, please specify ZIP-code');
        }

        if ($errorMsg && $showMethod) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorMsg);
            return $error;
        } elseif ($errorMsg) {
            return false;
        }
        return $this;
    }
    
    
    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        if ($request->getFreeMethodWeight()==$request->getPackageWeight()
            || !$request->hasFreeMethodWeight()) {
            return;
        }

        if (!$freeMethod = $this->getConfigData('free_method')) {
            return;
        }
        $freeRateId = false;
        
		$matchFedexGround = false;
        if ($this->getConfigData('free_both_ground')) {
	        if ( $freeMethod=='GROUND_HOME_DELIVERY') {
	        	$altMethod='FEDEX_GROUND';
	        	$matchFedexGround = true;
	        } else if ($freeMethod=='FEDEX_GROUND') {
	        	// could match on either, need to check
	        	$altMethod='GROUND_HOME_DELIVERY';
	        	$matchFedexGround = true;
	        }
		}

        if (is_object($this->_result)) {
            foreach ($this->_result->getAllRates() as $i=>$item) {
                if ($item->getMethod()==$freeMethod|| 
                	($matchFedexGround && $item->getMethod()==$altMethod)) {
                    $freeRateId = $i;
                    break;
                }
            }
        }

        if ($freeRateId===false) {
            return;
        }
        $price = null;
        if ($request->getFreeMethodWeight()>0) {
            $this->_setFreeMethodRequest($freeMethod);

            $result = $this->_getQuotes();
            if ($result && ($rates = $result->getAllRates()) && count($rates)>0) {
                if ((count($rates) == 1) && ($rates[0] instanceof Mage_Shipping_Model_Rate_Result_Method)) {
                    $price = $rates[0]->getPrice();
                }
                if (count($rates) > 1) {
                    foreach ($rates as $rate) {
                        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Method && $rate->getMethod() == $freeMethod
                        	|| ($matchFedexGround && $item->getMethod()==$altMethod)) {
                            $price = $rate->getPrice();
                        }
                    }
                }
            }
        } else {
            /**
             * if we can apply free shipping for all order we should force price
             * to $0.00 for shipping with out sending second request to carrier
             */
            $price = 0;
        }

        /**
         * if we did not get our free shipping method in response we must use its old price
         */
        if (!is_null($price)) {
            $this->_result->getRateById($freeRateId)->setPrice($price);
        }
    }

    /*********************************************************************************************
     * ALL METHODS BELOW ARE REQUIRED FOR 1.4.0.1 ONLY - SEE ORIGINAL UPS FILE
     *********************************************************************************************/

	/**
     * Get correct weigt.
     *
     * Namely:
     * Checks the current weight to comply with the minimum weight standards set by the carrier.
     * Then strictly rounds the weight up until the first significant digit after the decimal point.
     *
     * @param float|integer|double $weight
     * @return float
     */
    protected function _getCorrectWeight($weight)
    {
    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
    		return Mage_Usa_Model_Shipping_Carrier_Fedex::_getCorrectWeight($weight);
    	}
        $minWeight = $this->getConfigData('min_package_weight');

        if($weight < $minWeight){
            $weight = $minWeight;
        }

        //rounds a number to one significant figure
        $weight = ceil($weight*10) / 10;

        return $weight;
    }

   /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
    	if (method_exists(get_parent_class($this), '_debug')) {
    		return Mage_Usa_Model_Shipping_Carrier_Fedex::_debug($debugData);
    	}
        if ($this->getDebugFlag()) {
            Mage::log($debugData);
        }
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->getConfigData('debug');
    }
}

