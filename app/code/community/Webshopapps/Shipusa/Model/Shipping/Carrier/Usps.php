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
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * USPS shipping rates estimation
 *
 * @link       http://www.usps.com/webtools/htm/Development-Guide-v3-0b.htm
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/* UsaShipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Shipusa_Model_Shipping_Carrier_Usps
extends Mage_Usa_Model_Shipping_Carrier_Usps
{

	public function setRequest(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!Mage::getStoreConfig('shipping/shipusa/active')) {
			return parent::setRequest($request);
		}
		parent::setRequest($request);
		$r = $this->_rawRequest;
		$r->setIgnoreFreeItems(false);
		$r->setMaxPackageWeight($this->getConfigData('max_package_weight'));

		/* WSA change */

		if ($request->getUspsUserId() != '') {
			$r->setUspsUserid($request->getUspsUserId());
		} else {
			$r->setUspsUserid($this->getConfigData('userid'));
		}

		if ($request->getUspsPassword() != '') {
			$r->setUspsPassword($request->getUspsPassword());
		} else {
			$r->setUspsPassword($this->getConfigData('password'));
		}

		return $this;
	}

	/**
	 * Build RateV4 request, send it to USPS gateway and retrieve quotes in XML format
	 *
	 * @link http://www.usps.com/webtools/htm/Rate-Calculators-v2-3.htm
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	protected function _getXmlQuotes()
	{
	    if (!Mage::getStoreConfig('shipping/shipusa/active')) {
				return parent::_getXmlQuotes();
		}

		$r = $this->_rawRequest;
		$boxes = Mage::getSingleton('shipusa/dimcalculate')->getBoxes($this->_request->getAllItems(),$r->getMaxPackageWeight(),$r->getIgnoreFreeItems());
        $flatBoxes = Mage::getSingleton('shipusa/dimcalculate')->getBoxes($this->_request->getAllItems(),$r->getMaxPackageWeight(),$r->getIgnoreFreeItems(),true);

		$this->_numBoxes=count($boxes);
		$splitIndPackage = $this->getConfigData('break_multiples');
		$splitMaxWeight = $this->getConfigData('max_multiple_weight');
		$maxPackageWeight = $r->getMaxPackageWeight();
        $flatFound = count($flatBoxes) > 0;
        $largestFlatIdFound = 0;
        $flatBoxTypes = array();

	 	if (!Mage::helper('wsacommon')->checkItems('c2hpcHBpbmcvc2hpcHVzYS9zaGlwX29uY2U=',
			'aWdsb29tZQ==','c2hpcHBpbmcvc2hpcHVzYS9zZXJpYWw=')) {
        	Mage::log('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIERpbWVuc2lvbmFsIFNoaXBwaW5n');
        	return Mage::getModel('shipping/rate_result');
		}


		if ($this->_isUSCountry($r->getDestCountryId())) {
			$xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><RateV4Request/>');

			$xml->addAttribute('USERID', $r->getUserId());
			$xml->addChild('Revision', '2');
            $xmlFlat = clone $xml;
			$boxCounter=0;
            $flatCounter=0;

			foreach ($boxes as $box) {
                $billableWeight =  $this->_getCorrectWeight($box['weight']) ;

				if ($splitIndPackage && is_numeric($splitMaxWeight) && $splitMaxWeight> $maxPackageWeight &&
				$billableWeight<$splitMaxWeight) {
					for ($remainingWeight=$billableWeight;$remainingWeight>0;) {

						if ($remainingWeight-$maxPackageWeight<0) {
							$billableWeight=$remainingWeight;
							$remainingWeight=0;
						} else {
							$billableWeight=$maxPackageWeight;
							$remainingWeight-=$maxPackageWeight;
						}
                        $this->addPackage($box,$xml,$boxCounter,$billableWeight,$r,false);
					}
				} else {
                    $this->addPackage($box,$xml,$boxCounter,$billableWeight,$r,false);
                }
			}
            if($flatFound) {
                foreach ($flatBoxes as $flatBox) {
                    $billableWeight =  $this->_getCorrectWeight($flatBox['weight']) ;

                    $boxId = $flatBox['flat_box_id'];
                    if ($boxId > 3) {
                        $boxId = $flatBox['flat_type'];
                    }

                    if(!in_array($boxId, $flatBoxTypes)) {
                        $flatBoxTypes[] = $boxId;
                    }

                    $largestFlatIdFound = $boxId > $largestFlatIdFound ? $boxId : $largestFlatIdFound;

                    if ($splitIndPackage && is_numeric($splitMaxWeight) && $splitMaxWeight> $maxPackageWeight &&
                        $billableWeight<$splitMaxWeight) {
                        for ($remainingWeight=$billableWeight;$remainingWeight>0;) {

                            if ($remainingWeight-$maxPackageWeight<0) {
                                $billableWeight=$remainingWeight;
                                $remainingWeight=0;
                            } else {
                                $billableWeight=$maxPackageWeight;
                                $remainingWeight-=$maxPackageWeight;
                            }
                            $this->addPackage($flatBox,$xmlFlat,$flatCounter,$billableWeight,$r,true,$boxId);
                        }
                    } else {
                        $this->addPackage($flatBox,$xmlFlat,$flatCounter,$billableWeight,$r,true,$boxId);
                    }
                }
            }
			$api = 'RateV4';

		} else {
			$xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><IntlRateV2Request/>');

			$xml->addAttribute('USERID', $r->getUserId());
			$xml->addChild('Revision', '2');
            $xmlFlat = clone $xml;
            $boxCounter=0;
            $flatCounter=0;

            foreach ($boxes as $box) {
				$billableWeight =  $this->_getCorrectWeight($box['weight']);

				if ($splitIndPackage && is_numeric($splitMaxWeight) && $splitMaxWeight> $maxPackageWeight &&
					$billableWeight<$splitMaxWeight) {
					for ($remainingWeight=$billableWeight;$remainingWeight>0;) {

						if ($remainingWeight-$maxPackageWeight<0) {
							$billableWeight=$remainingWeight;
							$remainingWeight=0;
						} else {
							$billableWeight=$maxPackageWeight;
							$remainingWeight-=$maxPackageWeight;
						}

						$this->addIntPackage($box,$xml,$boxCounter,$billableWeight,$r);
					}
				} else {
			        $this->addIntPackage($box,$xml,$boxCounter,$billableWeight,$r);
				}
			}
            if ($flatFound) {
                foreach ($flatBoxes as $flatBox) {
                    $billableWeight =  $this->_getCorrectWeight($flatBox['weight']) ;

                    $boxId = $flatBox['flat_box_id'];
                    if ($boxId > 3) {
                        $boxId = $flatBox['flat_type'];
                    }

                    if(!in_array($boxId, $flatBoxTypes)) {
                        $flatBoxTypes[] = $boxId;
                    }

                    $largestFlatIdFound = $boxId > $largestFlatIdFound ? $boxId : $largestFlatIdFound;

                    if ($splitIndPackage && is_numeric($splitMaxWeight) && $splitMaxWeight> $maxPackageWeight &&
                        $billableWeight<$splitMaxWeight) {
                        for ($remainingWeight=$billableWeight;$remainingWeight>0;) {

                            if ($remainingWeight-$maxPackageWeight<0) {
                                $billableWeight=$remainingWeight;
                                $remainingWeight=0;
                            } else {
                                $billableWeight=$maxPackageWeight;
                                $remainingWeight-=$maxPackageWeight;
                            }
                            $this->addIntPackage($flatBox,$xmlFlat,$flatCounter,$billableWeight,$r,true,$boxId);
                        }
                    } else {
                        $this->addIntPackage($flatBox,$xmlFlat,$flatCounter,$billableWeight,$r,true,$boxId);
                    }
                }
            }

			$api = 'IntlRateV2';
		}

		$request = $xml->asXML();
        $flatRequest = $xmlFlat->asXML();

		$debugData = array('request' => Mage::helper('shipusa')->formatXML($request));
        $flatDebugData = array('request' => Mage::helper('shipusa')->formatXML($flatRequest));

        $responseBody = $this->_getCachedQuotes($request);
		if ($responseBody === null) {
			try {
				$url = $this->getConfigData('gateway_url');
				if (!$url) {
					$url = $this->_defaultGatewayUrl;
				}

	            $fullRequest = "API=".urlencode($api)."&XML=".urlencode($request);
	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            curl_setopt($ch, CURLOPT_HEADER, 0);
	            curl_setopt($ch, CURLOPT_POST, 2);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $fullRequest);
	            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            $responseBody = curl_exec ($ch);

				$debugData['result'] = Mage::helper('shipusa')->formatXML($responseBody);
				$this->_setCachedQuotes($request, $responseBody);
			}
			catch (Exception $e) {
				$debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
				$responseBody = '';
			}
			$this->_debug($debugData);
			if ($this->getDebugFlag()) {
	        	Mage::helper('wsacommon/log')->postMinor('usashipping','USPS Request/Response',$debugData);
	        }
		}
        //is everything assigned to a flat usps box? If so lets show flat boxes else dont
        if($flatFound) {
            $standardResult = $this->_parseWsaXmlResponse($debugData,$responseBody);
            $flatResponse = $this->executeFlatRequest($api, $flatRequest, $flatDebugData);

            $flatAllowedMethod = $this->getFlatBoxAllowedMethods($largestFlatIdFound);
            $nonApplicableMethods = array_diff($this->getFlatBoxAllowedMethods($flatBoxTypes), $flatAllowedMethod);

            return $this->_parseWsaXmlResponse($flatDebugData, $flatResponse, $standardResult, $flatAllowedMethod, $nonApplicableMethods);
        } else {
		    return $this->_parseWsaXmlResponse($debugData,$responseBody);
        }
	}

    public function getFlatBoxAllowedMethods($boxCode){

        $allowedMethods = array();
        $isUS = $this->_isUSCountry($this->_rawRequest->getDestCountryId());

        if (!is_array($boxCode)) {
            $boxCode = array($boxCode);
        }

        foreach ($boxCode as $code) {
            switch ($code) {
                case 1: //'SM FLAT RATE BOX':
                    $allowedMethods[] = $isUS ? 'Priority Mail Small Flat Rate Box' : 'Priority Mail International Small Flat Rate Box';
                    break;
                case 2: //'MD FLAT RATE BOX':
                    $allowedMethods[] = $isUS ? 'Priority Mail Medium Flat Rate Box' : 'Priority Mail International Medium Flat Rate Box';
                    break;
                case 3: //'LG FLAT RATE BOX':
                    $allowedMethods[] = $isUS ? 'Priority Mail Large Flat Rate Box' : 'Priority Mail International Large Flat Rate Box';
                    break;
                default:
                    //$allowedMethods[] = 'Priority Mail Medium Flat Rate Box';
                    //$allowedMethods[] = 'Priority Mail International Medium Flat Rate Box';
                    break;
            }
        }

        return $allowedMethods;
    }

    private function getBoxTypeCode($code){
        switch($code) {
            case 'SM FLAT RATE BOX':
                return 1;
                break;
            case 'MD FLAT RATE BOX':
                return 2;
                break;
            case 'LG FLAT RATE BOX':
                return 3;
                break;
            default:
                return 2;
                break;
        }
    }

    private function executeFlatRequest($api, $request, &$debugData) {
        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            try {
                $url = $this->getConfigData('gateway_url');
                if (!$url) {
                    $url = $this->_defaultGatewayUrl;
                }

                $fullRequest = "API=".urlencode($api)."&XML=".urlencode($request);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 2);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fullRequest);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $responseBody = curl_exec ($ch);

                $debugData['result'] = Mage::helper('wsacommon')->formatXML($responseBody);
                $this->_setCachedQuotes($request, $responseBody);
            }
            catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                $responseBody = '';
            }
            if ($this->getDebugFlag()) {
                Mage::helper('wsacommon/log')->postMinor('usashipping','USPS Request/Response',$debugData);
            }
        }

        return $responseBody;
    }

	private function addIntPackage($box,&$xml,&$boxCounter,$billableWeight,$r,$flatOnly = false,$boxId = 0) {
		$weightPounds = floor($billableWeight);
		$weightOunces = round(($billableWeight-floor($billableWeight)) * 16, 1);

		$package = $xml->addChild('Package');
		$package->addAttribute('ID', $boxCounter);

		$package->addChild('Pounds', $weightPounds);
		//  $package->addChild('Ounces', $r->getWeightOunces());
		$package->addChild('Ounces', $weightOunces); // WSA round to closest lb

		$package->addChild('Machinable', $r->getMachinable());
		$package->addChild('MailType', 'Package');

		$package->addChild('ValueOfContents',number_format($r->getValue(),2));  //Always has to be here

		$package->addChild('Country', $r->getDestCountryName());

        if ($flatOnly){
            $boxName = Mage::getModel('shipusa/shipping_carrier_source_flatbox')->getCode('usps_box',$boxId);
            $package->addChild('Container', $boxName);
            $package->addChild('Size', 'REGULAR');
        } else {
            if ($box['length']<=0) {
                if ( (strtoupper($r->getContainer()) == 'FLAT RATE ENVELOPE' ||
                strtoupper($r->getContainer()) == 'FLAT RATE BOX')) {
                    $package->addChild('Container', $r->getContainer());
                } else {
                    $package->addChild('Container', '');
                }
            }
        }

        if ($box['length']>0) {
            $package->addChild('Container', 'RECTANGULAR');
            if(!$flatOnly) {
                $package->addChild('Size', 'LARGE');
            }
            $package->addChild('Width', $box['width']);
            $package->addChild('Length', $box['length']);
            $package->addChild('Height', $box['height']);
            $package->addChild('Girth', (2* ($box['height']+$box['width'])));  //TODO Add as separate attribute
        } else {
            if(!$flatOnly) {
                $package->addChild('Size', $r->getSize());
            }
            $package->addChild('Width', '');
            $package->addChild('Length', '');
            $package->addChild('Height', '');
            $package->addChild('Girth', '');  //TODO Add as separate attribute
        }


		if ($this->getConfigFlag('monetary_value')) {
			$specialServices = $package->addChild('ExtraServices');
			$specialServices->addChild('ExtraService','1');
		}

		$boxCounter++;
	}

	private function addPackage($box,&$xml,&$boxCounter,$billableWeight,$r,$flatOnly = false,$boxId = 0) {
		$weightPounds = floor($billableWeight);
		$weightOunces = round(($billableWeight-floor($billableWeight)) * 16, 1);

		$package = $xml->addChild('Package');
		$package->addAttribute('ID', $boxCounter);

        if($flatOnly) {
            $service = 'PRIORITY';
        } else {
            $service = $this->getCode('service_to_code', $r->getService());
            if (!$service) {
                $service = $r->getService();
            }
        }

        $package->addChild('Service', $service);

		// no matter Letter, Flat or Parcel, use Parcel
		if ($r->getService() == 'FIRST CLASS') {
			$package->addChild('FirstClassMailType', 'PARCEL');
		}
		$package->addChild('ZipOrigination', $r->getOrigPostal());
		//only 5 chars avaialble
		$package->addChild('ZipDestination', substr($r->getDestPostal(),0,5));
		$package->addChild('Pounds',$weightPounds);
		//  $package->addChild('Ounces', $r->getWeightOunces());
		$package->addChild('Ounces', $weightOunces); // WSA round to closest lb

        if ($flatOnly){
            $boxName = Mage::getModel('shipusa/shipping_carrier_source_flatbox')->getCode('usps_box',$boxId);
            $package->addChild('Container', $boxName);
            $package->addChild('Size', 'REGULAR');
        } else {
		    // Because some methods don't accept VARIABLE and (NON)RECTANGULAR containers
            if ($box['length']<=0) {
                if ( (strtoupper($r->getContainer()) == 'FLAT RATE ENVELOPE' ||
                strtoupper($r->getContainer()) == 'FLAT RATE BOX')) {
                    $package->addChild('Container', $r->getContainer());
                } else {
                    $package->addChild('Container', '');
                }
            }

            if ($box['length']>0) {
                $package->addChild('Container', 'RECTANGULAR');
                $package->addChild('Size', 'LARGE');
                $package->addChild('Width', $box['width']);
                $package->addChild('Length', $box['length']);
                $package->addChild('Height', $box['height']);
                $package->addChild('Girth', (2* ($box['height']+$box['width'])));  //TODO Add as separate attribute
            } else {
                $package->addChild('Size', $r->getSize());
            }
        }

		if ($this->getConfigFlag('monetary_value')) {
			$package->addChild('Value',number_format($box['price'],2)); //TODO work for boxes
			$specialServices = $package->addChild('SpecialServices');
			$specialServices->addChild('SpecialService','1');
		}
		$package->addChild('Machinable', $r->getMachinable());
		$boxCounter++;
	}

    /**
     * Parse calculated rates
     *
     * @link http://www.usps.com/webtools/htm/Rate-Calculators-v2-3.htm
     * @param $debugData
     * @param string $response
     * @param Mage_Shipping_Model_Rate_Result $existingResult
     * @return Mage_Shipping_Model_Rate_Result
     */
	protected function _parseWsaXmlResponse($debugData,$response,$existingResult=array(),$allowedFlatMethod=array(),$flatBoxTypes=array())
	{
		if (!Mage::getStoreConfig('shipping/shipusa/active')) {
			return parent::_parseXmlResponse($response);
		}

		$costArr = array();
		$priceArr = array();
		$foundMethods=array();
		$requestType = $this->getConfigData('request_type');
		if (strlen(trim($response)) > 0) {
			if (strpos(trim($response), '<?xml') === 0) {
				if (preg_match('#<\?xml version="1.0"\?>#', $response)) {
					$response = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="ISO-8859-1"?>', $response);
				}

				$xml = simplexml_load_string($response);
				if (is_object($xml)) {
					$r = $this->_rawRequest;

					/* WSA change */
                    if(count($existingResult)) {
                        if(!is_array($allowedFlatMethod)) {
                            $allowedMethods = array($allowedFlatMethod);
                        } else {
                            $allowedMethods = $allowedFlatMethod;
                        }
                    } else {
                        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropship', 'carriers/dropship/active')) {
                            $allowedMethods = $this->_request->getUspsAllowedMethods();

                            if ($allowedMethods == null) {
                                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
                            }
                        } else {
                            $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
                        }
                    }

					/*
					 * US Domestic Rates
					 */
					$firstTimeRound=true;
                    $additionalRatePrice = 0;
					if ($this->_isUSCountry($r->getDestCountryId())) {
						if (is_object($xml->Package)) {
							foreach ($xml->Package as $package) {
								reset($foundMethods);
								if (is_object($package->Postage)) {
									foreach ($package->Postage as $postage) {
										$serviceName = $this->_filterServiceName((string)$postage->MailService);
										$postage->MailService = $serviceName;
										if (in_array($serviceName, $allowedMethods)) {
											// now get insurance
											$insurancePrice=0;
											if ($this->getConfigData('display_insurance')!='none') {
												if (is_object($package->Postage->SpecialServices) && is_object($package->Postage->SpecialServices->SpecialService)) {
													foreach ($package->Postage->SpecialServices->SpecialService as $specialService) {
														if ($specialService->ServiceID == 1) {
															$insurancePrice = $specialService->Price;
															break;
														}
													}
													if ($this->getConfigData('display_insurance')=='optional') {
														//TODO
													}

												}
											}

											if ($requestType=='ACCOUNT' && !empty($postage->CommercialRate)) {
												$ratePrice = (string)$postage->CommercialRate;
											} else {
												$ratePrice = (string)$postage->Rate;
											}

											if ($firstTimeRound) {
												$costArr[$serviceName] = $ratePrice;
												$priceArr[$serviceName] = $this->getMethodPrice($ratePrice + $insurancePrice, $serviceName);
											} else {
												if (array_key_exists($serviceName, $priceArr)) {
													$costArr[$serviceName] += $ratePrice;
													$priceArr[$serviceName] += $this->getMethodPrice($ratePrice + $insurancePrice, $serviceName);
													$foundMethods[$serviceName]=0;
												} // else ignore
											}
										} else if (in_array($serviceName, $flatBoxTypes) && count($existingResult)) {
                                            if ($requestType=='ACCOUNT' && !empty($postage->CommercialRate)) {
                                                $additionalRatePrice += (string)$postage->CommercialRate;
                                            } else {
                                                $additionalRatePrice += (string)$postage->Rate;

                                            }
                                            foreach ($allowedMethods as $meth) {
                                                $foundMethods[$meth]=0;

                                            }
                                        }
									}
								}
								if (!$firstTimeRound) {
									$unwantedArr = array_diff_key($priceArr,$foundMethods);
									$priceArr = array_diff_key($priceArr,$unwantedArr);
									$costArr = array_diff_key($costArr,$unwantedArr);
								}

                                if(count($existingResult)) {
                                    foreach ($allowedMethods as $method) {
                                        $priceArr[$method] += $additionalRatePrice;
                                        $additionalRatePrice = 0;
                                    }
                                }

								$firstTimeRound=false;
							}
							asort($priceArr);
						}
					} else {
						/*
						 * International Rates
						 */
                        $flatError=false;
                        $insurancePrice=0;
                        $additionalRatePrice = 0;
						if (is_object($xml->Package)) {
							foreach ($xml->Package as $package) {
                                $skipCounter = 0;
								reset($foundMethods);
								if (is_object($package->Service)) {
									foreach ($package->Service as $service) {
                                        $serviceCounter = count($package->Service);
										$serviceName = $this->_filterServiceName((string)$service->SvcDescription);
										$service->SvcDescription = $serviceName;
                                        if (count($existingResult)) {
                                            //International returns ALL services E.g small flat rate for a large flat rate box. Remove what we dont need
                                            $method = $this->getFlatBoxAllowedMethods($this->getBoxTypeCode($service->Container));
                                            if (!in_array($serviceName, $method)) {
                                                $skipCounter++;
                                                if($serviceCounter == $skipCounter){
                                                    $flatError = true;
                                                }
                                                continue;
                                            }
                                        }
										if (in_array($serviceName, $allowedMethods)) {
											if ($firstTimeRound) {
												$costArr[$serviceName] = (string)$service->Postage;
												$priceArr[$serviceName] = $this->getMethodPrice((string)$service->Postage + $insurancePrice, $serviceName);
											} else {
												if (array_key_exists($serviceName, $priceArr)) {
													$costArr[$serviceName] += (string)$service->Postage;
													$priceArr[$serviceName] += $this->getMethodPrice((string)$service->Postage + $insurancePrice, $serviceName);
													$foundMethods[$serviceName]=0;
												}
											}
										} else if (in_array($serviceName, $flatBoxTypes) && count($existingResult)) {
                                            $additionalRatePrice += (string)$service->Postage;;

                                            foreach ($allowedMethods as $meth) {
                                                $foundMethods[$meth]=0;
                                            }
                                        }
									}
								}

								if (!$firstTimeRound) {
									$unwantedArr = array_diff_key($priceArr,$foundMethods);
									$priceArr = array_diff_key($priceArr,$unwantedArr);
									$costArr = array_diff_key($costArr,$unwantedArr);
								}

                                if(count($existingResult)) {
                                    foreach ($allowedMethods as $method) {
                                        if($additionalRatePrice > 0) {
                                            if(array_key_exists($method, $priceArr)){
                                                $priceArr[$method] += $additionalRatePrice;
                                            } else {
                                                $priceArr[$method] = $additionalRatePrice;
                                            }
                                            $additionalRatePrice = 0;
                                        }
                                    }
                                }

								$firstTimeRound=false;
							}
                            if($flatError){
                                foreach ($allowedMethods as $method) {
                                    unset($priceArr[$method]);
                                    Mage::helper('wsacommon/log')->postCritical('usashipping','No USPS Flat Rate found','Check weight does not exceed max allowed for destination.');
                                }
                            }
							asort($priceArr);
						}
					}
				}
			}
		}

		if ($this->getDebugFlag()) {
        	Mage::helper('wsacommon/log')->postMinor('usashipping','USPS Response Prices',$priceArr);
        }

        if(!is_object($existingResult)) {
		    $result = Mage::getModel('shipping/rate_result');
            $foundStandard = false;
        } else {
            $result = $existingResult;
            $foundStandard = true;
        }
		if (empty($priceArr) && !$foundStandard) {
			$error = Mage::getModel('shipping/rate_result_error');
			$error->setCarrier('usps');
			$error->setCarrierTitle($this->getConfigData('title'));
			$error->setErrorMessage($this->getConfigData('specificerrmsg'));
			$result->append($error);
		  	Mage::helper('wsacommon/log')->postCritical('usashipping','No rates found',$debugData);
		} else {
            if (empty($priceArr)) {
                if ($this->getDebugFlag()) {
                    Mage::helper('wsacommon/log')->postMinor('usashipping','Error retriving Flat Box USPS Rates','No Allowed Rates Found');
                }
                return $result;
            }
			foreach ($priceArr as $method=>$price) {
				$rate = Mage::getModel('shipping/rate_result_method');
				$rate->setCarrier('usps');
				$rate->setCarrierTitle($this->getConfigData('title'));
				$rate->setMethod($method);
				$rate->setMethodTitle($method);
				$rate->setCost($costArr[$method]);
				$rate->setPrice($price);
				$result->append($rate);
			}
		}

		return $result;
	}


    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|bool
     */
    public function getCode($type, $code='')
    {
    	if (!Mage::getStoreConfig('shipping/shipusa/active')) {
			return parent::getCode($type, $code);
		}
        $codes = array(

            'service'=>array(
                'FIRST CLASS' => Mage::helper('usa')->__('First-Class'),
                'PRIORITY'    => Mage::helper('usa')->__('Priority Mail'),
                'EXPRESS'     => Mage::helper('usa')->__('Express Mail'),
                'BPM'         => Mage::helper('usa')->__('Bound Printed Matter'),
                'PARCEL'      => Mage::helper('usa')->__('Parcel Post'),
                'MEDIA'       => Mage::helper('usa')->__('Media Mail'),
                'LIBRARY'     => Mage::helper('usa')->__('Library'),
            ),

            'service_to_code'=>array(
                'First-Class'                                   => 'FIRST CLASS',
                'First-Class Mail International Large Envelope' => 'FIRST CLASS',
                'First-Class Mail International Letter'         => 'FIRST CLASS',
                'First-Class Mail International Package'        => 'FIRST CLASS',
                'First-Class Mail International Parcel'         => 'FIRST CLASS',
            	'First-Class Package International Service'     => 'FIRST CLASS',
            	'First-Class Mail'                 => 'FIRST CLASS',
                'First-Class Mail Flat'            => 'FIRST CLASS',
                'First-Class Mail Large Envelope'  => 'FIRST CLASS',
                'First-Class Mail International'   => 'FIRST CLASS',
                'First-Class Mail Letter'          => 'FIRST CLASS',
                'First-Class Mail Parcel'          => 'FIRST CLASS',
                'First-Class Mail Package'         => 'FIRST CLASS',
                'Parcel Post'                      => 'PARCEL',
            	'Standard Post'                    => 'PARCEL',
                'Bound Printed Matter'             => 'BPM',
                'Media Mail'                       => 'MEDIA',
                'Library Mail'                     => 'LIBRARY',
                'Express Mail'                     => 'EXPRESS',
                'Express Mail PO to PO'            => 'EXPRESS',
                'Express Mail Flat Rate Envelope'  => 'EXPRESS',
                'Express Mail Flat-Rate Envelope Sunday/Holiday Guarantee'  => 'EXPRESS',
                'Express Mail Sunday/Holiday Guarantee'            => 'EXPRESS',
                'Express Mail Flat Rate Envelope Hold For Pickup'  => 'EXPRESS',
                'Express Mail Hold For Pickup'                     => 'EXPRESS',
                'Global Express Guaranteed (GXG)'                  => 'EXPRESS',
                'Global Express Guaranteed Non-Document Rectangular'     => 'EXPRESS',
                'Global Express Guaranteed Non-Document Non-Rectangular' => 'EXPRESS',
                'USPS GXG Envelopes'                               => 'EXPRESS',
                'Express Mail International'                       => 'EXPRESS',
                'Express Mail International Flat Rate Envelope'    => 'EXPRESS',
                'Priority Mail'                        => 'PRIORITY',
                'Priority Mail Small Flat Rate Box'    => 'PRIORITY',
                'Priority Mail Medium Flat Rate Box'   => 'PRIORITY',
                'Priority Mail Large Flat Rate Box'    => 'PRIORITY',
                'Priority Mail Flat Rate Box'          => 'PRIORITY',
                'Priority Mail Flat Rate Envelope'     => 'PRIORITY',
                'Priority Mail International'                            => 'PRIORITY',
                'Priority Mail International Flat Rate Envelope'         => 'PRIORITY',
                'Priority Mail International Small Flat Rate Box'        => 'PRIORITY',
                'Priority Mail International Medium Flat Rate Box'       => 'PRIORITY',
                'Priority Mail International Large Flat Rate Box'        => 'PRIORITY',
                'Priority Mail International Flat Rate Box'              => 'PRIORITY'
            ),

            'first_class_mail_type'=>array(
                'LETTER'      => Mage::helper('usa')->__('Letter'),
                'FLAT'        => Mage::helper('usa')->__('Flat'),
                'PARCEL'      => Mage::helper('usa')->__('Parcel'),
            ),

            'container'=>array(
                'VARIABLE'           => Mage::helper('usa')->__('Variable'),
                'FLAT RATE BOX'      => Mage::helper('usa')->__('Flat-Rate Box'),
                'FLAT RATE ENVELOPE' => Mage::helper('usa')->__('Flat-Rate Envelope'),
                'RECTANGULAR'        => Mage::helper('usa')->__('Rectangular'),
                'NONRECTANGULAR'     => Mage::helper('usa')->__('Non-rectangular'),
            ),

            'containers_filter' => array(
                array(
                    'containers' => array('VARIABLE'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Express Mail Flat Rate Envelope',
                                'Express Mail Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Flat Rate Envelope',
                                'Priority Mail Large Flat Rate Box',
                                'Priority Mail Medium Flat Rate Box',
                                'Priority Mail Small Flat Rate Box',
                                'Express Mail',
                                'Priority Mail',
                                'Parcel Post',
                                'Standard Post',
                                'Media Mail',
                                'First-Class Mail Large Envelope',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'Express Mail International Flat Rate Envelope',
                                'Priority Mail International Flat Rate Envelope',
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                                'Global Express Guaranteed (GXG)',
                                'USPS GXG Envelopes',
                                'Express Mail International',
                                'Priority Mail International',
                                'First-Class Mail International Package',
                                'First-Class Mail International Large Envelope',
                                'First-Class Mail International Parcel',
                                'First-Class Package International Service',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('FLAT RATE BOX'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Priority Mail Large Flat Rate Box',
                                'Priority Mail Medium Flat Rate Box',
                                'Priority Mail Small Flat Rate Box',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'Priority Mail International Large Flat Rate Box',
                                'Priority Mail International Medium Flat Rate Box',
                                'Priority Mail International Small Flat Rate Box',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('FLAT RATE ENVELOPE'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Express Mail Flat Rate Envelope',
                                'Express Mail Flat Rate Envelope Hold For Pickup',
                                'Priority Mail Flat Rate Envelope',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'Express Mail International Flat Rate Envelope',
                                'Priority Mail International Flat Rate Envelope',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('RECTANGULAR'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Express Mail',
                                'Priority Mail',
                                'Parcel Post',
                                'Standard Post',
                                'Media Mail',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'USPS GXG Envelopes',
                                'Express Mail International',
                                'Priority Mail International',
                                'First-Class Mail International Package',
                                'First-Class Mail International Parcel',
                                'First-Class Package International Service',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('NONRECTANGULAR'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'Express Mail',
                                'Priority Mail',
                                'Parcel Post',
                                'Standard Post',
                                'Media Mail',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'Global Express Guaranteed (GXG)',
                                'USPS GXG Envelopes',
                                'Express Mail International',
                                'Priority Mail International',
                                'First-Class Mail International Package',
                                'First-Class Mail International Parcel',
                                'First-Class Package International Service',
                            )
                        )
                    )
                ),
             ),

            'size'=>array(
                'REGULAR'     => Mage::helper('usa')->__('Regular'),
                'LARGE'       => Mage::helper('usa')->__('Large'),
            ),

            'machinable'=>array(
                'true'        => Mage::helper('usa')->__('Yes'),
                'false'       => Mage::helper('usa')->__('No'),
            ),

            'delivery_confirmation_types' => array(
                'True' => Mage::helper('usa')->__('Not Required'),
                'False'  => Mage::helper('usa')->__('Required'),
            ),
            'insurance'=>array(
                'mandatory'        	=> Mage::helper('usa')->__('Compulsory'),
                //   'optional'       	=> Mage::helper('usa')->__('Optional'), //TODO
            	'none'				=> Mage::helper('usa')->__('None'),
            ),
        );

        $methods = $this->getConfigData('methods');
        if (!empty($methods)) {
            $codes['method'] = explode(",", $methods);
        } else {
            $codes['method'] = array();
        }

        if (!isset($codes[$type])) {
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }



	public function getResponse()
	{
		if (!Mage::getStoreConfig('shipping/shipusa/active')) {
			return parent::getResponse();
		}
		$statuses = '';
		if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result) {
			if ($trackings = $this->_result->getAllTrackings()) {
				foreach ($trackings as $tracking) {
					if($data = $tracking->getAllData()) {
						if (!empty($data['track_summary'])) {
							$statuses .= Mage::helper('usa')->__($data['track_summary']);
						} else {
							$statuses .= Mage::helper('usa')->__('Empty response');
						}
					}
				}
			}
		}
		if (empty($statuses)) {
			$statuses = Mage::helper('usa')->__('Empty response');
		}
		return $statuses;
	}

	/*****************************************************************
	 * COMMON CODE- If change here change in Fedex, UPS
	 */

	public function getTotalNumOfBoxes($weight)
	{
		if (!Mage::getStoreConfig('shipping/shipusa/active')) {
			return parent::getTotalNumOfBoxes($weight);
		}
		if (!Mage::getStoreConfig('shipping/shipusa/active')) {
			return parent::getTotalNumOfBoxes($weight);
		}

		$this->_numBoxes = 1; // now set up with dimensional weights
		$weight = $this->convertWeightToLbs($weight);
		return $weight;
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
			return parent::proccessAdditionalValidation($request);
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
			Mage::helper('wsacommon/log')->postNotice('usashipping','Inbuilt UPS Handling Fee',$finalMethodPrice-$cost);
		}
		return $finalMethodPrice;
	}



	protected function _setFreeMethodRequest($freeMethod)
	{
		if (!Mage::getStoreConfig('shipping/shipusa/active')) {
				return parent::_setFreeMethodRequest($freeMethod);
		}
		parent::_setFreeMethodRequest($freeMethod);
		$this->_rawRequest->setIgnoreFreeItems(true);


	}

 	/**
     * Determine whether zip-code is required for the country of destination
     *
     * @param string|null $countryId
     * @return bool
     */
    public function isZipCodeRequired($countryId = null)
    {
        if ($countryId != null) {
            return !Mage::helper('directory')->isZipCodeOptional($countryId);
        }
        return true;
    }




	/*********************************************************************************************
	 * ALL METHODS BELOW ARE REQUIRED FOR 1.6.0 and before ONLY - SEE ORIGINAL USPS FILE
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
			return parent::_getCorrectWeight($weight);
		}
		$minWeight = $this->getConfigData('min_package_weight');

		if($weight < $minWeight){
			$weight = $minWeight;
		}

		//rounds a number to one significant figure
		$weight = ceil($weight*10) / 10;

		return $weight;
	}






}
