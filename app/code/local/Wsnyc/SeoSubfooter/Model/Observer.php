<?php

class Wsnyc_SeoSubfooter_Model_Observer {
    
    /**
     * Add SEO Subfooter fields to CMS Meta Data tab
     * 
     * @param Varien_Event_Observer $observer
     */
    public function addSeoSubfooterFieldToMetaData(Varien_Event_Observer $observer) {
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('meta_fieldset');
        
        $fieldset->addField('seosubfooter_show', 'select', array(
            'name' => 'seosubfooter_show',
            'label' => Mage::helper('cms')->__('Show SEO Subfooter'),
            'title' => Mage::helper('cms')->__('Show SEO Subfooter'),
            'options' => Mage::getModel('eav/entity_attribute_source_boolean')->getOptionArray(),
        ));
        
        $blurbSelection = Mage::getModel('seosubfooter/source_blurbs')->getAllOptions();        
        $fieldset->addField('seosubfooter_blurb', 'multiselect', array(
            'name' => 'seosubfooter_blurb[]',
            'label' => Mage::helper('cms')->__('Limit Blurbs Selection'),
            'title' => Mage::helper('cms')->__('Limit Blurbs Selection'),
            'values' => $blurbSelection,
        ));

        $fieldset->addField('seosubfooter_text', 'editor', array(
            'name' => 'seosubfooter_text',
            'label' => Mage::helper('cms')->__('SEO Subfooter Text'),
            'title' => Mage::helper('cms')->__('SEO Subfooter Text'),
            'style' => 'height:26em;',
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
        ));
    }
    
    public function addSeoSubfooterFieldToMainForm(Varien_Event_Observer $observer) {
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('base_fieldset');
        
        $fieldset->addField('seosubfooter_link', 'select', array(
            'name' => 'seosubfooter_link',
            'label' => Mage::helper('cms')->__('SEO Landing Page'),
            'title' => Mage::helper('cms')->__('SEO Landing Page'),
            'note'      => Mage::helper('cms')->__('If this is SEO Landing Page it will be used in the link section of the blurb'),
            'options' => Mage::getModel('eav/entity_attribute_source_boolean')->getOptionArray(),
        ));
    }
    
    public function prepareCmsLimitFieldData(Varien_Event_Observer $observer) {
        $page = $observer->getEvent()->getPage();
        $page->setSeosubfooterBlurb(implode(',', $page->getSeosubfooterBlurb()));
    }
    
    public function prepareAskLimitFieldData(Varien_Event_Observer $observer) {
        $model = $observer->getEvent()->getObject();
        Mage::log($model->debug());
        if (is_array($model->getSeosubfooterBlurb())) {
            $model->setSeosubfooterBlurb(implode(',', $model->getSeosubfooterBlurb()));
        }
        Mage::log($model->debug());
    }
    
    /**
     * Save current CMS Page
     * 
     * @param Varien_Event_Observer $observer
     */
    public function saveCurrentPage(Varien_Event_Observer $observer) {
        Mage::register('current_page', $observer->getEvent()->getPage());
    }
    
    /**
     * Set value for current Ask the Laundress subpage
     * 
     * @param Varien_Event_Observer $observer
     */
    public function setAskPageBlurb(Varien_Event_Observer $observer) {
        $categoryId = Mage::app()->getRequest()->getParam('id');
        $category = Mage::getModel('wsnyc_questionsanswers/category')->load($categoryId);
        Mage::register('ask_category', $category);
    }
}