<?php

class Wsnyc_SeoSubfooter_Block_Adminhtml_Blurb_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('blurb_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('seosubfooter')->__('General'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('seosubfooter')->__('General Information'),
            'title' => Mage::helper('seosubfooter')->__('General Information'),
            'content' => $this->getLayout()->createBlock('seosubfooter/adminhtml_blurb_edit_tab_form')->toHtml(),
        ));
       
        //allow other modules add tabs to the edit page
        Mage::dispatchEvent('seosubfooter_prepare_blurb_form_tabs', array('block' => $this));

        return parent::_beforeToHtml();
    }

}
