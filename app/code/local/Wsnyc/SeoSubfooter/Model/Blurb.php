<?php

class Wsnyc_SeoSubfooter_Model_Blurb extends Mage_Core_Model_Abstract {

    /**
     * Event prefix name used in magento object related events
     * 
     * @var string
     */
    protected $_eventPrefix = 'blurb';

    /**
     * Event argument name
     * 
     * @var string
     */
    protected $_eventObject = 'blurb';

    /**
     * Constructor function
     *
     * @return void
     */
    public function _construct() {
        $this->_init('seosubfooter/blurb');
        parent::_construct();
    }

    public function getFilteredContent() {
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        return $processor->filter($this->getBlurbContent());
    }

}
