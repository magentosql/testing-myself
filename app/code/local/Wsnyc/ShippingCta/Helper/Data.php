<?php
/**
 * Created by PhpStorm.
 * User: ajewula
 * Date: 07.05.15
 * Time: 14:59
 */ 
class Wsnyc_ShippingCta_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isActive() {
        return Mage::getStoreConfig('shipping/option/shipping_cta_active');
    }

    public function getShippingCtaText() {
        return $this->__(Mage::getStoreConfig('shipping/option/shipping_cta_text'));
    }
}