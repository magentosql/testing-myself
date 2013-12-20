<?php

class Wsnyc_CheckoutCustomization_Model_Config extends Mage_Core_Model_Config_Data
{
    CONST XML_PATH_SETTINGS_REGULAR_GIFT_WRAP_FEE           = 'sales/gift_options/regular_gift_wrap_fee';
    CONST XML_PATH_SETTINGS_IS_HOLIDAY_GIFT_WRAP_ENABLED    = 'sales/gift_options/gift_wrap_holiday';
    CONST XML_PATH_SETTINGS_HOLIDAY_GIFT_WRAP_FEE           = 'sales/gift_options/holiday_gift_wrap_fee';

    /**
     * @return bool
     */
    public function isHolidayEnabled()
    {
        $enabled = Mage::getStoreConfig(self::XML_PATH_SETTINGS_IS_HOLIDAY_GIFT_WRAP_ENABLED, $this->getStoreId());
        return $enabled;
    }


    public function getRegularGiftWrapFee()
    {
        $str = Mage::getStoreConfig(self::XML_PATH_SETTINGS_REGULAR_GIFT_WRAP_FEE, $this->getStoreId());
        return $str;
    }


    public function getHolidayGiftWrapFee()
    {
        $str = Mage::getStoreConfig(self::XML_PATH_SETTINGS_HOLIDAY_GIFT_WRAP_FEE, $this->getStoreId());
        return $str;
    }
}