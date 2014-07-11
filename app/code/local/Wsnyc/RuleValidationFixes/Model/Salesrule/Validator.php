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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * SalesRule Validator Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wsnyc_RuleValidationFixes_Model_Salesrule_Validator extends Mage_SalesRule_Model_Validator {
    protected function _canProcessRule($rule, $address)
    {
        $alreadyAppliedRulesIds = explode(',', Mage::getSingleton('checkout/session')->getQuote()->getAppliedRuleIds());
        if(!empty($alreadyAppliedRulesIds) || strlen($alreadyAppliedRulesIds[0]) < 1) {
            foreach ($alreadyAppliedRulesIds as $appliedRuleId) {
                $ruleModel = Mage::getModel('salesrule/rule')->load($appliedRuleId);
                /* @var $ruleModel Mage_SalesRule_Model_Rule */
                if(!$ruleModel->isObjectNew() && $ruleModel->getStopRulesProcessing() && ($ruleModel->getSortOrder() < $rule->getSortOrder())) {
                    return false;
                }
            }
        }
        
        $cond = unserialize($rule->getConditionsSerialized());
        foreach($cond['conditions'] as $condition) {
            if($condition['attribute'] == 'quote_id') {
                $quote = Mage::getModel('checkout/session')->getQuote();                        
                $totals = $quote->getTotals();
                foreach ($totals as $total) {
                    if($total->getCode() == 'subtotal') {
                        $subtotal = $total->getValue();
                    } elseif ($total->getCode() == 'discount') {
                        $discount = $total->getValue();
                    }
                }
                $discountedData = $subtotal + $discount;
                if($condition['operator'] == '>=') {
                    if($discountedData < $condition['value']) {
                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                }
                if($condition['operator'] == '>') {
                    if($discountedData <= $condition['value']) {
                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                }
            }
        }
        
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        /**
         * check per coupon usage limit
         */
        if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON) {
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $coupon = Mage::getModel('salesrule/coupon');
                $coupon->load($couponCode, 'code');
                if ($coupon->getId()) {
                    // check entire usage limit
                    if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                    // check per customer usage limit
                    $customerId = $address->getQuote()->getCustomerId();
                    if ($customerId && $coupon->getUsagePerCustomer()) {
                        $couponUsage = new Varien_Object();
                        Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon(
                            $couponUsage, $customerId, $coupon->getId());
                        if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            $rule->setIsValidForAddress($address, false);
                            return false;
                        }
                    }
                }
            }
        }

        /**
         * check per rule usage limit
         */
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            $customerId     = $address->getQuote()->getCustomerId();
            $ruleCustomer   = Mage::getModel('salesrule/rule_customer');
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    $rule->setIsValidForAddress($address, false);
                    return false;
                }
            }
        }
        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForAddress($address, true);
        return true;
    }
    public function processFreeShipping(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $address = $this->_getAddress($item);
        $item->setFreeShipping(false);
        $allRules = $this->_getRules();
        $sortedRules = array();
        
        foreach ($this->_getRules() as $rule) {
            if(!empty($sortedRules)) {
                $firstItem = $sortedRules[0];
                if($firstItem->getSortOrder() < $rule->getSortOrder()) {
                    $sortedRules[] = $rule;
                } else {
                    array_unshift($sortedRules, $rule);
                }
            } else {
                $sortedRules[] = $rule;
            }
            
        }
        
        $rulesToApply = array();
        foreach ($sortedRules as $rule) {
            $rulesToApply[] = $rule;
            if($rule->getStopRulesProcessing()) {
                break;
            }
        }        
        foreach ($rulesToApply as $rule) {
            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            switch ($rule->getSimpleFreeShipping()) {
                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM:
                    $item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
                    break;

                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS:
                    $address->setFreeShipping(true);
                    break;
            }
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }
        return $this;
    }
}
