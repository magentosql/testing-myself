<?php

class Wsnyc_CategoryDescriptions_Model_Resource_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    public function _construct() {        
        $this->_init('wsnyc_categorydescriptions/rule');
    }
    
    /**
     * Filter rules collection by store
     * 
     * @param int $store
     * @return \Wsnyc_CategoryDescriptions_Model_Resource_Rule_Collection
     */
    public function addStoreFilter($store = null) {
        if (null === $store) {
            $store = Mage::app()->getStore()->getStoreId();
        }        
        $this->join(array('store' => 'wsnyc_categorydescriptions/store'), 'main_table.rule_id = store.rule_id', null);
        $this->addFieldToFilter('store.store_id', array('in' => array(Mage_Core_Model_App::ADMIN_STORE_ID, $store)));
        
        return $this;
    }
    
    public function addTimeRestrictions() {
        
        $now = date('Y-m-d');
        $this->addFieldToFilter(
            array('to_date', 'to_date'),
            array(array('gteq' => $now), array('null' => 'null')));
        $this->addFieldToFilter(
            array('from_date', 'from_date'),
            array(array('lteq' => $now), array('null' => 'null')));
        return $this;
    }
    
    public function orderByPriority() {
        $this->setOrder('sort_order', Varien_Data_Collection_Db::SORT_ORDER_DESC);
        return $this; 
   }
}