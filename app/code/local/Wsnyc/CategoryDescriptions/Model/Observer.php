<?php

class Wsnyc_CategoryDescriptions_Model_Observer {
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     */
    public function setCategoryDescription(Varien_Event_Observer $observer) {
       
        $object = new Varien_Object();        
        $object->setCategoryId(Mage::registry('current_category')->getId());
        $object->setFilters($this->_getUsedFilters());
        if ($rule = $this->_findRule($object)) {
            Mage::register('current_rule', $rule, true);
        }
    }
    
    protected function _findRule(Varien_Object $object) {
        
        $rules = Mage::getModel('wsnyc_categorydescriptions/rule')->getCollection()
                        ->addStoreFilter()
                        ->addTimeRestrictions()
                        ->orderByPriority();
        
        foreach($rules as $rule) {            
            if ($rule->getConditions()->validate($object)) {
                //found matching rule
                return $rule;
            }
        }
        
        return false;
    }
    
    protected function _getUsedFilters() {
        
        $layer = Mage::getSingleton('catalog/layer');
        
        $filters = array();        
        foreach ($layer->getState()->getFilters() as $filter) {            
            /**
             * @var Mage_Catalog_Model_Layer_Filter_Item $filter
             */
            if ($filter->getFilter()->getData('attribute_model')) {
                $attribute = $filter->getFilter()->getAttributeModel();                
                if (!array_key_exists($attribute->getAttributeCode(), $filters)) {
                    $filters[$attribute->getAttributeCode()] = array();
                }
                $filters[$attribute->getAttributeCode()][] = $filter->getValue();   
            }
        }
        return $filters;
    }
}