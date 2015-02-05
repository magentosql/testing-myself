<?php
class Wsnyc_PromoModals_Block_Modal extends Mage_Core_Block_Template {

    /**
     * Active modals collection
     * 
     * @var Wsnyc_PromoModals_Model_Resource_Modal_Collection
     */
    protected $_collection;

    /**
     * Get active modals
     * 
     * @return Wsnyc_PromoModals_Model_Resource_Modal_Collection
     */
    public function getModals() {
        if (null === $this->_collection) {
            $this->_collection = Mage::getModel('promomodals/modal')->getCollection()
                                ->addStatusFilter()
                                ->addSort();
        }        
        return $this->_collection;
    }
    
    /**
     * If module is inactive do not produce any output
     * 
     * @return string
     */
    protected function _toHtml() {
        if (!Mage::getStoreConfig('wsnyc_promotions/promotions/active')) {
            return '';
        }
        return parent::_toHtml();
    }
}