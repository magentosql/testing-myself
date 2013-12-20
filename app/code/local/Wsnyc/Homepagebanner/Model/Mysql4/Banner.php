<?php

class Wsnyc_Homepagebanner_Model_Mysql4_Banner extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('wsnyc_homepagebanner/banner', 'banner_id');
    }
    
    public function getExistingBannersBySpecifiedIds($bannerIds, $isActive = true)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('*'))
            ->where('banner_id IN (?)', $bannerIds);
        if ($isActive) {
            $select->where('status = ?', (int)$isActive);
        }
        $select->order('FIELD(banner_id, '.implode(',',$bannerIds).')');
        
        return $adapter->fetchAll($select);
    }    
    

}
