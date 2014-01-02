<?php

class Wsnyc_Homepagebanner_Model_Mysql4_Banner extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('wsnyc_homepagebanner/banner', 'banner_id');
    }


    public function getAllActiveBanners()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('*'))
            ->where('status = ?', 1)
            ->order('position ASC');


        return $adapter->fetchAll($select);
    }
    

}
