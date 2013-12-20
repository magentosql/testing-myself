<?php

class Wsnyc_Homepagebanner_Block_Adminhtml_Banner_Edit
	extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'wsnyc_homepagebanner';
        $this->_controller = 'adminhtml_banner';

        $this->_updateButton('save', 'label', Mage::helper('wsnyc_homepagebanner')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('wsnyc_homepagebanner')->__('Delete Banner'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('wsnyc_homepagebanner_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'wsnyc_homepagebanner_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'wsnyc_homepagebanner_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('wsnyc_homepagebanner_data') && Mage::registry('wsnyc_homepagebanner_data')->getId()) {
            return Mage::helper('wsnyc_homepagebanner')->__("Edit Banner '%s'", $this->htmlEscape(Mage::registry('wsnyc_homepagebanner_data')->getTitle()));
        } else {
            return Mage::helper('wsnyc_homepagebanner')->__('Add Banner');
        }
    }
}