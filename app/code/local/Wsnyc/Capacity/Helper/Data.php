<?php

class Wsnyc_Capacity_Helper_Data extends Mage_Core_Helper_Abstract {
    
    protected $_fedexMethods = array(
        'GROUND_HOME_DELIVERY' => 'FedEx Home',
        'FEDEX_GROUND' => 'FedEx Ground',
        'FEDEX_2_DAY' => 'FedEx 2nd Day Air',
        'FIRST_OVERNIGHT' => 'FedEx Priority Overnight',
        'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight',
        'INTERNATIONAL_ECONOMY' => 'International Economy',
        'INTERNATIONAL_GROUND' => 'International Ground',
        'INTERNATIONAL_PRIORITY' => 'International Priority'
    );
    
    protected $_otherMethod = 'Other';

    public function getRegion($address) {
        if (in_array($address->getCountry(), array('US', 'CA')) && $address->getRegionId()) {
            $region = Mage::getModel('directory/region')->load($address->getRegionId());
            return $region->getCode();
        }
        return null;
    }
    
    public function getStreet($address, $line = 1) {
        $street = $address->getStreet(-1);        
        if (strlen($street) > 128) {
            $streetLines = explode("\n", wordwrap($street, 200, "\n"));            
        }
        else {
            $streetLines = array(0 => $street, 1 => null);
        }
        
        return $line == 1 ? $streetLines[0] : $streetLines[1];
    }
    
    public function getShippingMethod($shipment) {
        $method = $shipment->getUdropshipMethod();
        if (strstr($method, 'fedex_')) {
            $fedexMethod = str_replace('fedex_', null, $method);        
            if (array_key_exists($fedexMethod, $this->_fedexMethods)) {
                return $this->_fedexMethods[$fedexMethod];
            }
            else {
                return $this->_otherMethod;
            }
        }
        
        return $this->_otherMethod;
    }

}
