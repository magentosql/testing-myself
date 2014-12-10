<?php

class Wsnyc_PromoModals_Model_Resource_Modal_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    /**
     * Define resource model
     *
     */
    protected function _construct() {
        $this->_init('promomodals/modal');
        parent::_construct();
    }

    /**
     * Add status field filter to dealer collection
     *
     * @param int $status
     * @return Zefir_Dealers_Model_Resource_Dealer_Collection $this
     */
    public function addStatusFilter($status = 1) {
        $this->addFieldToFilter('is_active', array('eq' => $status));
        return $this;
    }
}
