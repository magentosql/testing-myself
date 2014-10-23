<?php

class Wsnyc_CategoryDescriptions_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'wsnyc_categorydescriptions';
        $this->_controller = 'adminhtml_rule';

        $this->_updateButton('save', 'label', Mage::helper('wsnyc_categorydescriptions')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('wsnyc_categorydescriptions')->__('Delete Rule'));
    }

    public function getHeaderText() {
        if (Mage::registry('rule_data') && Mage::registry('rule_data')->getId()) {
            return Mage::helper('wsnyc_categorydescriptions')->__("Edit Rule '%s'", $this->htmlEscape(Mage::registry('rule_data')->getName()));
        } else {
            return Mage::helper('wsnyc_categorydescriptions')->__('Add Rule');
        }
    }

}
