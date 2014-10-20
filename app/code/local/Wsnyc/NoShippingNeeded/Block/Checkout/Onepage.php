<?php

class Wsnyc_NoShippingNeeded_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage {

    public function getSteps()
    {
        $steps = array();
        $stepCodes = $this->_getStepCodes();

        if ($this->isCustomerLoggedIn()) {
            if(Mage::helper('noshippingneeded')->checkIfShippingMethodStepShouldBeIgnored()) {
                $stepCodes = array_diff($stepCodes, array('login', 'shipping', 'shipping_method'));
            } else {
                $stepCodes = array_diff($stepCodes, array('login'));
            }
            
        }

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }
}
