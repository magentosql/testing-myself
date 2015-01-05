<?php

class Wsnyc_FlatRateExt_Model_Carrier_Flatrate extends Mage_Shipping_Model_Carrier_Flatrate {
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if ($this->_checkOrderAmount($request)) {
            return parent::collectRates($request);
        }
        return false;
    }

    /**
     * Check if order value fits flat rate application limits
     * 
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return boolean
     */
    protected function _checkOrderAmount(Mage_Shipping_Model_Rate_Request $request) {
        $min = $this->getConfigData('min_amount');
        $max = $this->getConfigData('max_amount');
        $value = $this->getConfigData('order_base') ? $request->getPackageValueWithDiscount() : $request->getPackageValue();
        if ($min && $min > $value) {
            return false;
        }
        if ($max && $max < $value) {
            return false;
        }        
        return true;
    }
}