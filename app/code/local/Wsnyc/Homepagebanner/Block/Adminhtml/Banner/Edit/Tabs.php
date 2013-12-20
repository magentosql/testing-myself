<?php
class Wsnyc_Homepagebanner_Block_Adminhtml_Banner_Edit_Tabs
	extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct() {
        parent::__construct();
        $this->setId('wsnyc_homepagebanner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('wsnyc_homepagebanner')->__('Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('General Information'),
            'title' => Mage::helper('wsnyc_homepagebanner')->__('General Information'),
            'content' => $this->getLayout()->createBlock('wsnyc_homepagebanner/adminhtml_banner_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
