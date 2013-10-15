<?php

class EmJaInteractive_SmartPost_Model_Carrier_Smart extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'smartpost';

    /**
     * Collect rates for this shipping method based on information in $request 
     * 
     * @param Mage_Shipping_Model_Rate_Request $data 
     * @return Mage_Shipping_Model_Rate_Result 
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {        
        if ($request->getPackageWeight() > 70) {
            return false;
        }
        
        $response = $this->getResponse($request);
        if ($response) {
            $result = Mage::getModel('shipping/rate_result');
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('title'));

            $shippingPrice = (float) $response;
            $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
            return $result;
        } else {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));

            return $error;
        }
    }

    private function getResponse(Mage_Shipping_Model_Rate_Request $request) {
        $data['WebAuthenticationDetail'] = array('UserCredential' =>
            array('Key' => $this->getConfigData('Key'), 'Password' => $this->getConfigData('Password')));
        $data['ClientDetail'] = array('AccountNumber' => $this->getConfigData('AccountNumber'), 'MeterNumber' => $this->getConfigData('MeterNumber'));
        $data['TransactionDetail'] = array('CustomerTransactionId' => ' *** SmartPost Rate Request v13 using PHP ***');
        $data['Version'] = array('ServiceId' => 'crs', 'Major' => '13', 'Intermediate' => '0', 'Minor' => '0');
        $data['ReturnTransitAndCommit'] = true;
        $data['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
        $data['RequestedShipment']['ShipTimestamp'] = date('c');
        $data['RequestedShipment']['ServiceType'] = 'SMART_POST'; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
        $data['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
        $data['RequestedShipment']['Shipper'] = array('Address' => array('StreetLines' => array($this->getConfigData('StreetLines')),
                'City' => $this->getConfigData('City'),
                'StateOrProvinceCode' => $this->getConfigData('StateOrProvinceCode'),
                'PostalCode' => $this->getConfigData('PostalCode'),
                'CountryCode' => $this->getConfigData('CountryCode')));
        $data['RequestedShipment']['Recipient'] = array('Address' => array('StreetLines' => array('13450 Farmcrest Ct'),
                'City' => $request->getDestCity(),
                'StateOrProvinceCode' => $request->getDestRegionCode(),
                'PostalCode' => $request->getDestPostcode(),
                'CountryCode' => $request->getDestCountryId()));
        $data['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
            'Payor' => array(
                'ResponsibleParty' => array(
                    'AccountNumber' => $this->getConfigData('AccountNumber'),
                    'CountryCode' => $this->getConfigData('CountryCode'))
            )
        );
        $data['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT';
        $data['RequestedShipment']['RateRequestTypes'] = 'LIST';
        $data['RequestedShipment']['SmartPostDetail'] = array('Indicia' => 'PARCEL_SELECT',
            'AncillaryEndorsement' => 'CARRIER_LEAVE_IF_NO_RESPONSE',
            'SpecialServices' => 'USPS_DELIVERY_CONFIRMATION',
            'HubId' => $this->getConfigData('HubId'),
            'CustomerManifestId' => $this->getConfigData('CustomerManifestId'));
        $data['RequestedShipment']['PackageCount'] = '1';
        $data['RequestedShipment']['RequestedPackageLineItems'] = array(
            'SequenceNumber' => 1,
            'GroupPackageCount' => 1,
            'Weight' => array('Value' => ceil($request->getPackageWeight()),
                'Units' => 'LB'),
            'Dimensions' => array('Length' => 10,
                'Width' => 10,
                'Height' => 10,
                'Units' => 'IN'));

        try {
//            $location = $this->getConfigData('ServiceURL');
//            $client->__setLocation($location);
//            $response = $client->getRates($data);
//            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
//                return $response;
//            }

            $url = Mage::getBaseURL('web'). 'smart.php';
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('data'=>json_encode($data)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $result = curl_exec($ch);
            curl_close($ch);
            
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        return array($this->_code => $this->getConfigData('name'));
    }

}

