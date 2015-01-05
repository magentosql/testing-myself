<?php

class Wsnyc_PromoModals_Model_Resource_Modal extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct() {
        $this->_init('promomodals/modal', 'modal_id');
    }

}
