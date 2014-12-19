<?php

/**
 * Blurb list source class
 * 
 */
class Wsnyc_SeoSubfooter_Model_Source_Blurbs extends Mage_Core_Model_Abstract {

    protected $_list;
    
    public function toOptionHash() {

        if (null === $this->_list) {
            $this->_prepareList();
        }
        return $this->_list;        
    }
    
    public function getAllOptions() {
        return $this->toOptionArray();
    }
    
    public function toOptionArray() {
        if (null === $this->_list) {
            $this->_prepareList();
        }
        $list = array();
        foreach($this->_list as $blurbId => $title) {
            $list[] = array(
                'label' => $title,
                'value' => $blurbId
            );
        }
        
        return $list;
    }
    
    protected function _prepareList() {
        $collection = Mage::getModel('seosubfooter/blurb')->getCollection()->setOrder('title', 'ASC');
        $list = array();
        foreach($collection as $blurb) {
            $list[$blurb->getId()] = $blurb->getTitle();
        }
        $this->_list = $list;
    }

}
