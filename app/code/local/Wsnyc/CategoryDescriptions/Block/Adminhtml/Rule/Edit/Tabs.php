<?php

class Wsnyc_CategoryDescriptions_Block_Adminhtml_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('wsnyc_categorydescriptions')->__('New Rule'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('wsnyc_categorydescriptions')->__('General'),
            'title' => Mage::helper('wsnyc_categorydescriptions')->__('General'),
            'content' => $this->getLayout()->createBlock('wsnyc_categorydescriptions/adminhtml_rule_edit_tab_form')->toHtml(),
        ));
        $this->addTab('conditions_section', array(
            'label' => Mage::helper('wsnyc_categorydescriptions')->__('Conditions'),
            'title' => Mage::helper('wsnyc_categorydescriptions')->__('Conditions'),
            'content' => $this->getLayout()->createBlock('wsnyc_categorydescriptions/adminhtml_rule_edit_tab_conditions')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
