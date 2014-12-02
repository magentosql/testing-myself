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
        Mage::register('show_blurb', $category->getSeosubfooterShow());
    }
}