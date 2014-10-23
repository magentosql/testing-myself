<?php

class Wsnyc_CategoryDescriptions_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'wsnyc_categorydescriptions';
        $this->_headerText = Mage::helper('wsnyc_categorydescriptions')->__('Category Description Manager');
        $this->_addButtonLabel = Mage::helper('wsnyc_categorydescriptions')->__('Add Rule');
        parent::__construct();
    }

}
