<?php

class Wsnyc_SeoSubfooter_Model_Observer {
    
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
    
    public function saveCurrentPage(Varien_Event_Observer $observer) {
        Mage::register('current_page', $observer->getEvent()->getPage());
    }
    
    public function setAskPageBlurb(Varien_Event_Observer $observer) {
        $categoryId = Mage::app()->getRequest()->getParam('id');
        $category = Mage::getModel('wsnyc_questionsanswers/category')->load($categoryId);
        Mage::register('show_blurb', $category->getSeosubfooterShow());
    }
}