<?php

class Wsnyc_Homepagebanner_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getCfg($optionString)
    {
        return Mage::getStoreConfig('wsnyc_homepagebanner/' . $optionString);
    }
}