<?php

class Wsnyc_SeoSubfooter_Block_Footer extends Mage_Core_Block_Template {
    
    /**
     * @var Wsnyc_SeoSubfooter_Model_Blurb
     */
    protected $_blurb;
    
    /**
     * @var Mage_Cms_Model_Resource_Page_Collection
     */
    protected $_links;


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
        elseif (Mage::registry('ask_category')) {
            return Mage::registry('ask_category')->getSeosubfooterShow();
        }
        return false;
    }
    
    public function getBlurb() {
        if (null === $this->_blurb) {
            $collection = Mage::getModel('seosubfooter/blurb')->getCollection()->setCurPage(1)->setPageSize(1)->addStatusFilter();
            $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));            
            if ($blurbs = $this->_getSelectedBlurbs()) {                
                $collection->addFieldToFilter('blurb_id', array('in' => $blurbs));
            }
            $this->_blurb = $collection->getFirstItem();
        }
        
        return $this->_blurb;
    }
    
    public function getLinks() {
        if (null === $this->_links) {
            $this->_links = Mage::getModel('cms/page')->getCollection()
                                    ->addFieldToFilter('seosubfooter_link', array('eq' => 1))
                                    ->addFieldToFilter('is_active', array('eq' => 1));
        }
        
        return $this->_links;
    }
    
    public function getPageUrl(Mage_Cms_Model_Page $page) {
        return Mage::getUrl(null, array('_direct' => $page->getIdentifier()));
    }
    
    protected function _getSelectedBlurbs() {
        
        $blurbs = false;
         if (Mage::registry('product')) {
            $blurbs = Mage::getResourceModel('catalog/product')->getAttributeRawValue(Mage::registry('product')->getId(), 'seosubfooter_blurb', Mage::app()->getStore());            
        }
        elseif (Mage::registry('current_category')) {
            $blurbs = Mage::getResourceModel('catalog/category')->getAttributeRawValue(Mage::registry('current_category')->getId(), 'seosubfooter_blurb', Mage::app()->getStore());            
        }        
        elseif (Mage::registry('current_page')) {
            $blurbs = Mage::registry('current_page')->getSeosubfooterBlurb();            
        }
        elseif (Mage::registry('ask_category')) {
            $blurbs = Mage::registry('ask_category')->getSeosubfooterBlurb();
        }
        
        return $blurbs ? explode(',',$blurbs) : false;
    }
}