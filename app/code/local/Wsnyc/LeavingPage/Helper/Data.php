<?php

class Wsnyc_LeavingPage_Helper_Data extends Mage_Core_Helper_Abstract {

    public function generatePromoCode() {
        $rule = Mage::getModel('salesrule/rule')->load(Mage::getStoreConfig('wsnyc/leavingpage/rule_id'));

        $generator = Mage::getModel('salesrule/coupon_massgenerator');
        $generator->setFormat(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC);
        $generator->setDash(4);
        $generator->setLength(12);
        $generator->setPrefix('LV-');
        $generator->setSuffix('');

        $rule->setCouponCodeGenerator($generator);
        $rule->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO);

        $coupon = $rule->acquireCoupon();
        $code = $coupon->getCode();
        
        return $code;
    }

}
