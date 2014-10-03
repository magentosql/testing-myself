<?php

class Wsnyc_TierDiscount_Model_Validator {
    
    
    /**
     * Rule type action
     */    
    const TIER_DISCOUNT_ACTION = 'tier_discount';
    
    
    /**
     * Check if rule uses tier discount
     * Fired on salesrule_validator_process event
     * 
     * @param Varien_Event_Observer $observer
     */
    public function validateRule(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $rule = $event->getRule(); 
        Mage::log($rule->debug());
        Mage::log($event->getQty());
        if ($rule->getSimpleAction() != self::TIER_DISCOUNT_ACTION) {
            return;
        }
        
    }

}