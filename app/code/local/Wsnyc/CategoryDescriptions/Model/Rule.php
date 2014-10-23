<?php

class Wsnyc_CategoryDescriptions_Model_Rule extends Mage_Core_Model_Abstract {
    
    public function _construct() {
        parent::_construct();
        $this->_init('wsnyc_categorydescriptions/rule');
    }
}