<?php

class Wsnyc_CategoryDescriptions_Model_Resource_Store extends Mage_Core_Model_Resource_Db_Abstract {
    
    public function _construct() {        
        $this->_init('wsnyc_categorydescriptions/store', array('rule_id', 'store_id'));
    }
}