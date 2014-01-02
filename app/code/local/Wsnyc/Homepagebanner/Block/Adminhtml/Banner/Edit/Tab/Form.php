<?php

class Wsnyc_Homepagebanner_Block_Adminhtml_Banner_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('wsnyc_homepagebanner_form', array('legend' => Mage::helper('wsnyc_homepagebanner')->__('General information')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            //$model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('filename', 'image', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Image File'),
            'required' => true,
            'name' => 'filename',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('wsnyc_homepagebanner')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('wsnyc_homepagebanner')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('weblink', 'text', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Web Url'),
            'required' => true,
            'name' => 'weblink',
        ));

        $fieldset->addField('webname', 'text', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Web Name'),
            'required' => true,
            'name' => 'webname',
        ));

        $fieldset->addField('banner_content', 'editor', array(
            'name' => 'banner_content',
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Content'),
            'title' => Mage::helper('wsnyc_homepagebanner')->__('Content'),
            'style' => 'width:280px; height:100px;',
            'required' => true,
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),

        ));

        $fieldset->addField('position', 'text', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Position'),
            'required' => true,
            'name' => 'position',
        ));


        if (Mage::getSingleton('adminhtml/session')->getBannerSliderData()) {
            $data = Mage::getSingleton('adminhtml/session')->getBannerSliderData();
            Mage::getSingleton('adminhtml/session')->setBannerSliderData(null);
        } elseif (Mage::registry('wsnyc_homepagebanner_data')) {
            $data = Mage::registry('wsnyc_homepagebanner_data')->getData();
        }
        $data['store_id'] = explode(',', $data['stores']);
        $form->setValues($data);

        return parent::_prepareForm();
    }

    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;
    }

}