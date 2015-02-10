<?php

class Wsnyc_CouponParam_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Generate url with coupon param
     *
     * @param string $coupon
     * @return string
     */
    public function generateCartCodeUrl($coupon) {
        return Mage::getUrl('checkout/cart', array(
            'secure' => Mage::app()->getRequest()->isSecure(),
            'coupon' => $coupon
        ));
    }
}