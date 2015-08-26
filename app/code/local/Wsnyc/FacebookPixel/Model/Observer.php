<?php

class Wsnyc_FacebookPixel_Model_Observer
{
    public function setRegisterFlag(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('customer/session')->setFbTrackRegistration(true);
    }
}
