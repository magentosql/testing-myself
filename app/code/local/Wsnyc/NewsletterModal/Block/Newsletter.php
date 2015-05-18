<?php

class Wsnyc_NewsletterModal_Block_Newsletter extends Mage_Core_Block_Template {

    protected function _construct() {
        parent::_construct();
        if(Mage::getStoreConfigFlag('newslettermodal/general/active')){
            $this->setTemplate('wsnyc/newslettermodal/newslettermodal.phtml');
        }
    }
}