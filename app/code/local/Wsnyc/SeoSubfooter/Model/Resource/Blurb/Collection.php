<?php

class Wsnyc_SeoSubfooter_Model_Resource_Blurb_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    /**
     * Define resource model
     *
     */
    protected function _construct() {
        $this->_init('seosubfooter/blurb');
        parent::_construct();
    }

    /**
     * Add status field filter to dealer collection
     *
     * @param int $status
     * @return Wsnyc_SeoSubfooter_Model_Resource_Blurb_Collection $this
     */
    public function addStatusFilter($status = Wsnyc_SeoSubfooter_Model_Source_Status::BLURB_STATUS_ENABLED) {
        $this->addFieldToFilter('status', array('eq' => $status));
        return $this;
    }

}
