<?php

class Wsnyc_SeoSubfooter_Block_Footer extends Mage_Core_Block_Template {
    
    /**
     *
     * @var Wsnyc_SeoSubfooter_Model_Blurb
     */
    protected $_blurb;


    protected function _construct() {
        $this->setTemplate('wsnyc/seosubfooter/blurb.phtml');
        return parent::_construct();
    }
    
    /**
     * Check if current page should show blurb
     * 
     * @todo Add attributes to ask the laundress page that would set this value
     * @return boolean
     */
    public function shouldShowBlurb() {
        
        if (Mage::registry('product')) {            
            return Mage::registry('product')->getSeosubfooterShow();
        }
        elseif (Mage::registry('current_category')) {            
            return Mage::registry('current_category')->getSeosubfooterShow();
        }        
        elseif (Mage::registry('current_page')) {
            return Mage::registry('current_page')->getSeosubfooterShow();
        }
        elseif (Mage::registry('show_blurb')) {
            return Mage::registry('show_blurb');
        }
        return false;
    }
    
    public function getBlurb() {
        if (null === $this->_blurb) {
            $collection = Mage::getModel('seosubfooter/blurb')->getCollection()->setCurPage(1)->setPageSize(1)->addStatusFilter();
            $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
            $this->_blurb = $collection->getFirstItem();
        }
        
        return $this->_blurb;
    }
}