<?php

class Wsnyc_Homepagebanner_Block_Banner extends Mage_Core_Block_Template
{
    protected $_bannerResource = null;
    protected $_sessionInstance = null;
    protected $_currentStoreId = null;

    protected function _construct() {

        parent::_construct();
        $this->_bannerResource = Mage::getResourceSingleton('wsnyc_homepagebanner/banner');
        $this->_currentStoreId = Mage::app()->getStore()->getId();
        $this->_sessionInstance = Mage::getSingleton('core/session');
    }

    public function getBanners()
    {
//        Mage::log($this->_bannerResource->getAllActiveBanners(), null, 'test.log');
        $banners = $this->_bannerResource->getAllActiveBanners();
        $this->setData('banners', $banners);

        return $this->_getData('banners');
    }

    public function getSlideshowCfg()
    {
        $h = Mage::helper('wsnyc_homepagebanner');

        $cfg = array();
        $cfg['navigation']			= $h->getCfg('general/navigation');
        $cfg['slideSpeed']		= $h->getCfg('general/slideSpeed');
        $cfg['paginationSpeed']		= $h->getCfg('general/paginationSpeed');
        $cfg['singleItem']		= $h->getCfg('general/singleItem');

        return $cfg;
    }
} 