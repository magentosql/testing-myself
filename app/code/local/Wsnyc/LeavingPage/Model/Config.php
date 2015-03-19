<?php

/**
 * Class Wsnyc_LeavingPage_Model_Config
 *
 */
class Wsnyc_LeavingPage_Model_Config
{
    public function getAllowedPages()
    {
        return explode(',', Mage::getStoreConfig('promo/leavingpage/allowed_pages'));
    }
}