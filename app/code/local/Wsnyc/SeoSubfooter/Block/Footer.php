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
            return true;
        }
        elseif (Mage::registry('current_category')) {
            return true;
        }        
        elseif (Mage::registry('current_page')) {
            return true;
        }
        elseif (Mage::registry('ask_category')) {
            return true;
        }
        return false;
    }

    public function getBlurb() {
        if (null === $this->_blurb) {
            $text = trim($this->_getCurrentObject()->getSeosubfooterText());
            if ($text) {
                $blurb = Mage::getModel('seosubfooter/blurb')->setData(array(
                    'blurb_content' => $text,
                    'show' => $this->_getCurrentObject()->getSeosubfooterShow()
                ));
            }
            else {
                $blurb = Mage::getModel('seosubfooter/blurb');
            }

            $this->_blurb = $blurb;
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

    /**
     * Get object of the current page
     *
     * @return bool|Varien_Object
     */
    protected function _getCurrentObject() {

        $object = false;
        if (Mage::registry('product')) {
            $object = Mage::registry('product');
        }
        elseif (Mage::registry('current_category')) {
            $object = Mage::registry('current_category');
        }
        elseif (Mage::registry('current_page')) {
            $object = Mage::registry('current_page');
        }
        elseif (Mage::registry('ask_category')) {
            $object = Mage::registry('ask_category');
        }

        return $object;
    }
}