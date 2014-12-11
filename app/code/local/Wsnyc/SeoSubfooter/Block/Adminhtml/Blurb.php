<?php

class Wsnyc_SeoSubfooter_Block_Adminhtml_Blurb extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_blurb';
        $this->_blockGroup = 'seosubfooter';
        $this->_headerText = Mage::helper('seosubfooter')->__('Blurbs Manager');
        $this->_addButtonLabel = Mage::helper('seosubfooter')->__('Add Blurb');
        parent::__construct();
    }

}
