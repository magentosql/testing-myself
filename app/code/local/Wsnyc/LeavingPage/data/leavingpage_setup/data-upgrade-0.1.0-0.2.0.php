<?php

$installer = $this;

$rule = Mage::getModel('salesrule/rule')->setName('CUSTOMERS LEAVING PAGE')
        ->setDescription(null)
        ->setFromDate(null)
        ->setToDate(null)
        ->setUsesPerCustomer('1')
        ->setIsActive('1')
        ->setConditionsSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
        ->setActionsSerialized('a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
        ->setStopRulesProcessing('1')
        ->setIsAdvanced('1')
        ->setProductIds(null)
        ->setSortOrder('3')
        ->setSimpleAction('by_percent')
        ->setDiscountAmount(15.0000)
        ->setDiscountQty(null)
        ->setDiscountStep('0')
        ->setSimpleFreeShipping('0')
        ->setApplyToShipping('0')
        ->setTimesUsed('0')
        ->setIsRss('0')
        ->setCouponType('2')
        ->setUseAutoGeneration('1')
        ->setUsesPerCoupon(1000)
        ->setMaxdiscount(0)
        ->setTierDiscount('a:0:{}')
        ->setCustomerGroupIds(array(0, 1))
        ->setWebsiteIds(array(1))
        ->setStoreLabels(array('15 Percent Off'))
        ->save();

$installer->setConfigData('wsnyc/leavingpage/rule_id', $rule->getId(), 'default', 0);