<?php

class Wsnyc_FacebookPixel_Block_Pixel extends Mage_Core_Block_Template
{

    const XML_ENABLED = 'facebook_pixel/general/enabled';
    const XML_PIXEL_ID = 'facebook_pixel/general/pixel_id';
    const XML_CONVERSION_TRACKING = 'facebook_pixel/general/track_conversion';
    const XML_CONVERSION_PIXEL_ID = 'facebook_pixel/general/conversion_pixel_id';

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_ENABLED);
    }

    public function isCovnersionEnabled()
    {
        return Mage::getStoreConfig(self::XML_CONVERSION_TRACKING);
    }

    public function getPixelId()
    {
        return Mage::getStoreConfig(self::XML_PIXEL_ID);
    }

    public function getConversionPixelId()
    {
        return Mage::getStoreConfig(self::XML_CONVERSION_PIXEL_ID);
    }

    public function getAdditionalTracking()
    {
        $additionalTracking = '';
        if ($this->getAdditional()) {
            switch($this->getAdditional()) {
                case 'purchase':
                    $_order = $this->_getOrder();
                    $data = array(
                        'value' => round($_order->getBaseGrandTotal(),2),
                        'currency' => Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
                    );
                    $additionalTracking .= "fbq('track', 'Purchase', " . json_encode($data).");\n";
                break;
            }
        }

        if (Mage::getSingleton('customer/session')->getFbTrackRegistration(true)) {
            $additionalTracking .= "fbq('track', 'CompleteRegistration');\n";
        }

        return $additionalTracking;
    }

    protected function _getOrder()
    {
        return Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
    }
}
