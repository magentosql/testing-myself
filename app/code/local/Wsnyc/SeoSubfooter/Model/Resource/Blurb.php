<?php

class Wsnyc_SeoSubfooter_Model_Resource_Blurb extends Mage_Core_Model_Resource_Db_Abstract {

    /**
     * Initialize resource model
     */
    protected function _construct() {
        $this->_init('seosubfooter/blurb', 'blurb_id');
    }
}
