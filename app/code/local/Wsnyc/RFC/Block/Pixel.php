<?php 

class Wsnyc_RFC_Block_Pixel extends Mage_Core_Block_Template {

    const XML_ENABLED = 'rfc_pixel/general/enabled';
    const XML_PIXEL_ID = 'rfc_pixel/general/pixel_id';
    const XML_USER_ID = 'rfc_pixel/general/user_id';
    const XML_CONVERSION_TRACKING = 'rfc_pixel/general/track_conversion';
    const XML_CONVERSION_PIXEL_ID = 'rfc_pixel/general/conversion_pixel_id';

    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_ENABLED);
    }
    
    public function isCovnersionEnabled() {
        return Mage::getStoreConfig(self::XML_CONVERSION_TRACKING);
    }

    public function getUserId() {
        return Mage::getStoreConfig(self::XML_USER_ID);
    }

    public function getPixelId() {
        return Mage::getStoreConfig(self::XML_PIXEL_ID);
    }

    public function getConversionPixelId() {
        return Mage::getStoreConfig(self::XML_CONVERSION_PIXEL_ID);
    }
}
