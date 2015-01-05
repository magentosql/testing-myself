<?php 
class Wsnyc_Fedex_Model_Usa_Shipping_Carrier_Fedex extends Mage_Usa_Model_Shipping_Carrier_Fedex
{
    /**
     * Get origin based amount form response of rate estimation
     *
     * @param stdClass $rate
     * @return null|float
     */
    protected function _getRateAmountOriginBased($rate)
    {
        $amount = null;
        $rateTypeAmounts = array();
    
        if (is_object($rate)) {
            // The "RATED..." rates are expressed in the currency of the origin country
            foreach ($rate->RatedShipmentDetails as $ratedShipmentDetail) {
                $netAmount = (string)$ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                $rateType = (string)$ratedShipmentDetail->ShipmentRateDetail->RateType;
                $rateTypeAmounts[$rateType] = $netAmount;
            }
    
            // Order is important
            // WSNYC ADDED 'RATED_ACCOUNT_PACKAGE'
            //Mage::log('rated account package');
            foreach (array('RATED_ACCOUNT_SHIPMENT', 'RATED_ACCOUNT_PACKAGE', 'RATED_LIST_SHIPMENT', 'RATED_LIST_PACKAGE') as $rateType) {
                if (!empty($rateTypeAmounts[$rateType])) {
                    $amount = $rateTypeAmounts[$rateType];
                    break;
                }
            }
    
            if (is_null($amount)) {
                $amount = (string)$rate->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
            }
        }
    
        return $amount;
    }

    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
        $r = $this->_rawRequest;
        $ratesRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ),
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => array(
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                /*'TotalInsuredValue' => array(
                    'Amount'  => $r->getValue(),
                    'Currency' => '',/*$this->getCurrencyCode()
                ),*/
                'Shipper' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getOrigPostal(),
                        'CountryCode' => $r->getOrigCountry()
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery')
                    )
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => 'SENDER',
                    'Payor' => array(
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $r->getOrigCountry()
                    )
                ),
                'CustomsClearanceDetail' => array(
                    'CustomsValue' => array(
                        'Amount' => $r->getValue(),
                        'Currency' => $this->getCurrencyCode()
                    )
                ),
                'RateRequestTypes' => 'LIST',
                'PackageCount'     => '1',
                'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => array(
                    '0' => array(
                        'Weight' => array(
                            'Value' => (float)$r->getWeight(),
                            'Units' => 'LB'
                        ),
                        'GroupPackageCount' => 1,
                    )
                )
            )
        );

        if ($purpose == parent::RATE_REQUEST_GENERAL) {
            /*$ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = array(
                'Amount'  => $r->getValue(),
                'Currency' => $this->getCurrencyCode()
            );*/
        } else if ($purpose == parent::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = parent::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = array(
                'Indicia' => ((float)$r->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid')
            );
        }
        return $ratesRequest;
    }
    

    public function getMethodPrice($cost, $method = '') {
        if ($method == $this->getConfigData($this->_freeMethod)
            && $this->getConfigData('free_shipping_enable')
            && $this->getConfigData('free_shipping_subtotal') <= $this->_rawRequest->getValueWithDiscount()
        ) {
            $price = '0.00';
        }
        else {
            $price = $this->getFinalPriceWithHandlingFee($cost);
        }
        return $price;
    }

    /**
     * Add flat rate functionality
     * 
     * @param mixed $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _prepareRateResponse($response) {
        $result = parent::_prepareRateResponse($response);
        if ($this->getConfigData('flatrate_enable')) {
            $result = $this->_applyFlatrate($result);
        }
        return $result;
    }
    
    /**
     * Check and apply flat rate
     * 
     * @param mixed $result
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _applyFlatrate($result) {
        $request = $this->_request;
        $value = $this->getConfigData('order_base') ? $request->getPackageValueWithDiscount() : $request->getPackageValue();
        if ($value < $this->getConfigData('flatrate_subtotal')) {
            $flatrate_method = $this->getConfigData('flatrate_method');
            foreach($result->getAllRates() as $rate) {
                if ($rate->getMethod() == $flatrate_method) {
                    $price = $rate->getPrice();
                    $cost = $rate->getCost() - $price;
                    $rate->setPrice($this->getConfigData('flatrate_price'));
                    $rate->setCost($cost + $this->getConfigData('flatrate_price'));
                }
            }
        }
        return $result;
    }

}
