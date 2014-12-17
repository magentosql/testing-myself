<?php

class Wsnyc_PromoModals_Model_Modal extends Mage_Core_Model_Abstract {

    /**
     * Event prefix name used in magento object related events
     * 
     * @var string
     */
    protected $_eventPrefix = 'modal';

    /**
     * Event argument name
     * 
     * @var string
     */
    protected $_eventObject = 'modal';

    /**
     * Constructor function
     *
     * @return void
     */
    public function _construct() {
        $this->_init('promomodals/modal');
        parent::_construct();
    }

}
