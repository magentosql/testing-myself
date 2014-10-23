<?php

class Wsnyc_CategoryDescriptions_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract {

    public function _construct() {
        $this->_init('wsnyc_categorydescriptions/rule', 'rule_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('wsnyc_categorydescriptions/store'))
                ->where('rule_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('rule_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('wsnyc_categorydescriptions/store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['rule_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('wsnyc_categorydescriptions/store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['rule_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('wsnyc_categorydescriptions/store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }

}
