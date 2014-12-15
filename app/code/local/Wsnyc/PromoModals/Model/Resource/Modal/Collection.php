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
     * @return \Wsnyc_PromoModals_Model_Resource_Modal_Collection
     */
    public function addStatusFilter($status = 1) {
        $this->addFieldToFilter('main_table.modal_is_active', array('eq' => $status));
        if (Mage::getStoreConfig('wsnyc_promotions/promotions/check_rule')) {
            $this->join(array('rule' => 'salesrule/rule'), 'main_table.rule_id=rule.rule_id', '');
            $this->addFieldToFilter('rule.is_active', array('eq' => 1));
        }        
        return $this;
    }
    
    /**
     * Sort collection by modal_sort_order column
     * 
     * @return \Wsnyc_PromoModals_Model_Resource_Modal_Collection
     */
    public function addSort($direction = self::SORT_ORDER_ASC) {
        $this->setOrder('main_table.modal_sort_order', $direction);
        return $this;
    }
}
