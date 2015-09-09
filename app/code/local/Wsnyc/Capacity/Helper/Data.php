<?php

class Wsnyc_Capacity_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Map of magento shipping method code to capacity methods names
     *
     * @var array
     */
    protected $_fedexMethods = array(
        'GROUND_HOME_DELIVERY' => 'FedEx Home',
        'FEDEX_GROUND' => 'FedEx Ground',
        'FEDEX_2_DAY' => 'FedEx 2nd Day Air',
        'FIRST_OVERNIGHT' => 'FedEx Priority Overnight',
        'STANDARD_OVERNIGHT' => 'FedEx Standard Overnight',
        'INTERNATIONAL_ECONOMY' => 'International Economy',
        'INTERNATIONAL_GROUND' => 'International Ground',
        'INTERNATIONAL_PRIORITY' => 'International Priority',
        'SMART_POST' => 'Other',
        'percentageprice' => 'Fedex Ground' /** Wsnyc_PercentPriceShipping_Model_Carrier_Percentageprice */
    );

    /**
     * Default method
     * @var string
     */
    protected $_otherMethod = 'Other';

    /**
     * Get region code for shipping address
     *
     * @param Varien_Object $address
     * @return string|null
     */
    public function getRegion($address) {
        if (in_array($address->getCountry(), array('US', 'CA')) && $address->getRegionId()) {
            $region = Mage::getModel('directory/region')->load($address->getRegionId());
            return $region->getCode();
        }
        return null;
    }

    /**
     * Format street
     *
     * @param Varien_Object $address
     * @param int $line
     * @return mixed
     */
    public function getStreet($address, $line = 1) {
        $street = $address->getStreet(-1);
        if (strstr($street, "\n")) {
            $streetLines = explode("\n", $street);
        }
        elseif (strlen($street) > 128) {
            $streetLines = explode("\n", wordwrap($street, 200, "\n"));            
        }
        else {
            $streetLines = array(0 => $street, 1 => null);
        }
        
        return $line == 1 ? str_replace("\n", ' ', $streetLines[0]) : str_replace("\n", ' ', $streetLines[1]);
    }

    /**
     * Get Capacity shipping method code for magento shipping
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getShippingMethod(Mage_Sales_Model_Order $order) {

        if ($order->getShippingMethod() == 'udsplit_total') {
            //udropship module installed.
            $dropship = json_decode($order->getUdropshipShippingDetails());
            $method = $dropship->methods->{1}->code;
        }
        else {
            $method = $order->getShippingMethod();
        }

        if (strstr($method, 'fedex_')) {
            $fedexMethod = str_replace('fedex_', null, $method);        
            if (array_key_exists($fedexMethod, $this->_fedexMethods)) {
                return $this->_fedexMethods[$fedexMethod];
            }
            else {
                return $this->_otherMethod;
            }
        }
        elseif('freeshipping_freeshipping' === $method) {
            return $this->_fedexMethods['FEDEX_GROUND'];
        }
        
        return $this->_otherMethod;
    }

}
