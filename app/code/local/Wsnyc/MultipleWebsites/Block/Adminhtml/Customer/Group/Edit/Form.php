<?php

class Wsnyc_MultipleWebsites_Block_Adminhtml_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form {

    protected function _prepareLayout() {
        call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
        $form = new Varien_Data_Form();
        $customerGroup = Mage::registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('customer')->__('Group Information')));

        $validateClass = sprintf('required-entry validate-length maximum-length-%d', Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH);
        $name = $fieldset->addField('customer_group_code', 'text', array(
            'name' => 'code',
            'label' => Mage::helper('customer')->__('Group Name'),
            'title' => Mage::helper('customer')->__('Group Name'),
            'note' => Mage::helper('customer')->__('Maximum length must be less then %s symbols', Mage_Customer_Model_Group::GROUP_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
                )
        );

        if ($customerGroup->getId() == 0 && $customerGroup->getCustomerGroupCode()) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select', array(
            'name' => 'tax_class',
            'label' => Mage::helper('customer')->__('Tax Class'),
            'title' => Mage::helper('customer')->__('Tax Class'),
            'class' => 'required-entry',
            'required' => true,
            'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
                )
        );

        $fieldset->addField('group_type', 'select', array(
            'name' => 'group_type',
            'label' => Mage::helper('multiplewebsites')->__('Group Type'),
            'title' => Mage::helper('multiplewebsites')->__('Group Type'),
            'class' => 'required-entry',
            'required' => true,
            'values' => array(
                0 => 'Retailers',
                1 => 'Wholesalers',
                2 => 'Distributors'
            )
                )
        );

        $fieldset->addField('price_multiplier', 'text', array(
            'name' => 'price_multiplier',
            'label' => Mage::helper('multiplewebsites')->__('Price Multiplier'),
            'title' => Mage::helper('multiplewebsites')->__('Price Multiplier'),
            'class' => 'required-entry',
            'required' => true,
            'note' => Mage::helper('multiplewebsites')->__('E.g. a value of 0.85 means 15% discount.'),
                )
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden', array(
                'name' => 'id',
                'value' => $customerGroup->getId(),
                    )
            );
        }

        if (Mage::getSingleton('adminhtml/session')->getCustomerGroupData()) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
    }

}
