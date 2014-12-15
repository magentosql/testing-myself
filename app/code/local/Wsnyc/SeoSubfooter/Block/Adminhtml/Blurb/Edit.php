<?php

class Wsnyc_SeoSubfooter_Block_Adminhtml_Blurb_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'seosubfooter';
        $this->_controller = 'adminhtml_blurb';
 
        $this->_updateButton('save', 'label', Mage::helper('seosubfooter')->__('Save Blurb'));
        $this->_updateButton('delete', 'label', Mage::helper('seosubfooter')->__('Delete Blurb'));
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('blurb_data') && Mage::registry('blurb_data')->getId() ) {
            return Mage::helper('seosubfooter')->__("Edit Blurb '%s'", $this->htmlEscape(Mage::registry('blurb_data')->getDealerName()));
        } else {
            return Mage::helper('seosubfooter')->__('New Blurb');
        }
    }
}