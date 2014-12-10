<?php

class Wsnyc_TierDiscount_Model_Validator extends Mage_SalesRule_Model_Validator {
    
    
    /**
     * Rule type action
     */    
    const TIER_DISCOUNT_ACTION = 'tier_discount';
    
    protected $_cartQty;
    
    /**
     * Check if rule uses tier discount
     * Fired on salesrule_validator_process event
     * 
     * @param Varien_Event_Observer $observer
     */
    public function validateRule(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $rule = $event->getRule(); 
        
        if ($rule->getSimpleAction() != self::TIER_DISCOUNT_ACTION) {
            //not our action
            return;
        }
        
        $cartQty = $this->_getCartQty($event->getQuote(), $rule);
        $tierDiscounts = $this->_getTierValues($rule);        
        $discount = $this->_getDiscount($tierDiscounts, $cartQty);
        if (!$discount) {
            //do tier for this qty
            return;
        }
        
        $item = $event->getItem();
        $result = $event->getResult();
        $qty = $event->getQty();
        $itemPrice              = $this->_getItemPrice($item);
        $baseItemPrice          = $this->_getItemBasePrice($item);
        
        if ($discount['type'] == 'percent') {
            //calculate percent discount
            $rulePercent = (min(100, $discount['amount'])) / 100;
            $discountAmount    = ($qty*$itemPrice - $item->getDiscountAmount()) * $rulePercent;
            $baseDiscountAmount= ($qty*$baseItemPrice - $item->getBaseDiscountAmount()) * $rulePercent;
        }
        elseif ($discount['type'] == 'fixed') {
            //calculate fixed discount
            $discountAmount    = $qty * $discount['amount'];
            $baseDiscountAmount= $qty * $discount['amount'];
        }
        
        $result->setDiscountAmount($discountAmount);
        $result->setBaseDiscountAmount($baseDiscountAmount);
    }
    
    /**
     * Unserialize and sort tier values
     * 
     * @param Mage_SalesRule_Model_Rule $rule
     * @return array
     */
    protected function _getTierValues(Mage_SalesRule_Model_Rule $rule) {
        $tierDiscounts = unserialize($rule->getTierDiscount());
        usort($tierDiscounts, function($a, $b){
            if ($a['min_qty'] == $b['min_qty']) {
                return 0;
            }
            return $a['min_qty'] < $b['min_qty'] ? -1 : 1;
        });
        
        return $tierDiscounts;
    }
    
    /**
     * Get tier range for cart qty
     * 
     * @param array $tierDiscounts
     * @param int $qty
     * @return array|boolean
     */
    protected function _getDiscount($tierDiscounts, $qty) {
        $current = false;
        foreach($tierDiscounts as $range) {
            if ($qty >= $range['min_qty']) {
                $current = $range;
            }
            else {
                break;
            }
        }        
        return $current;
    }
    
    /**
     * Get quantity of all items in cart without fully discounted items
     * 
     * @param Mage_Sales_Model_Quote $quote
     * @return int
     */
    protected function _getCartQty(Mage_Sales_Model_Quote $quote, $rule) {
        if (null === $this->_cartQty) {
            $cartQty = 0;
            foreach($quote->getAllVisibleItems() as $item) {
                //count only items that are not fully discounted and that fulfil actions
                if ($item->getBasePrice() > 0 && $rule->getActions()->validate($item)) {
                    $cartQty += $item->getQty();
                }
            }
            $this->_cartQty = $cartQty;
        }
        return $this->_cartQty;
    }
}