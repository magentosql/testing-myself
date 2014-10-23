<?php

class Wsnyc_CategoryDescriptions_Block_Adminhtml_Rule_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('rule_form', array('legend' => Mage::helper('wsnyc_categorydescriptions')->__('General Information')));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('wsnyc_categorydescriptions')->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('wsnyc_categorydescriptions')->__('Status'),
            'name' => 'is_active',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('wsnyc_categorydescriptions')->__('Active'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('wsnyc_categorydescriptions')->__('Inactive'),
                ),
            ),
        ));
        
        $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('wsnyc_categorydescriptions')->__('Content'),
            'title' => Mage::helper('wsnyc_categorydescriptions')->__('Content'),
            'style' => 'width:100%; height:400px;',
            'wysiwyg' => true,
            'required' => true,
        ));

        if ( Mage::getSingleton('adminhtml/session')->getRuleData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getRuleData());
            Mage::getSingleton('adminhtml/session')->setRuleData(null);
        } elseif ( Mage::registry('rule_data') ) {
            $form->setValues(Mage::registry('rule_data')->getData());
        }
        return parent::_prepareForm();
    }

}
