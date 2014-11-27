<?php

class Wsnyc_SeoSubfooter_Block_Adminhtml_Blurb_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('general_form', array('legend' => Mage::helper('seosubfooter')->__('General')));
        $fieldset->addField('blurb_id', 'hidden', array('name' => 'blurb_id',));
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('seosubfooter')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        
        $fieldset->addField('url', 'text', array(
            'label' => Mage::helper('seosubfooter')->__('Link'),
            'required' => false,
            'name' => 'url',
        ));
        
        $fieldset->addField('blurb_content', 'editor', array(
            'label' => Mage::helper('seosubfooter')->__('Content'),
            'class' => 'required-entry',
            'required' => true,
            'style'     => 'height:15em;width: 20em',
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'name' => 'blurb_content',
            'wysiwyg'   => true,
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('seosubfooter')->__('Status'),
            'title' => Mage::helper('seosubfooter')->__('Status'),
            'name' => 'status',
            'required' => true,
            'options' => Mage::getSingleton('seosubfooter/source_status')->toOptionHash()
        ));

        //allow other modules add fields to the form
        Mage::dispatchEvent('seosubfooter_prepare_blurb_form_fields', array('form' => $this->getForm()));
        if (Mage::getSingleton('adminhtml/session')->getBlurbData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlurbData());
        } elseif (Mage::registry('blurb_data')) {
            if (!Mage::registry('blurb_data')->getId()) {
                Mage::registry('blurb_data')->setStatus(1);
            }
            $form->setValues(Mage::registry('blurb_data')->getData());
        }
        return parent::_prepareForm();
    }

}
