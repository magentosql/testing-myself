<?php

class Wsnyc_CategoryDescriptions_Model_Condition_Product_Attribute extends Mage_SalesRule_Model_Rule_Condition_Product_Combine {

    public function __construct() {
        parent::__construct();
        $this->setType('wsnyc_categorydescriptions/condition_product_attribute');
    }

    public function asHtml() {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('wsnyc_categorydescriptions')->__("If a category is filtered by %s of these attriubtes:", $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(Varien_Object $object) {
        $all = $this->getAggregator() == 'all';
        foreach($this->getConditions() as $condition) {
            $validated = $condition->validate($object);
            if ($all && !$validated) {
                return false;
            }
            if (!$all && $validated) {
                return true;
            }
        }        
        /**
         * At this point if all conditions have to be met there was no invalid
         * if any has to be met there was no valid
         */
        return $all ? true : false;
    }

    /**
     * Prepare and show available attributes to select
     * 
     * @return array
     */
    public function getNewChildSelectOptions() {
        $productCondition = Mage::getModel('wsnyc_categorydescriptions/condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $conditions = array(array('value'=>'', 'label'=>Mage::helper('rule')->__('Please choose a condition to add...')));
        foreach ($productAttributes as $code => $label) {
            $conditions[] = array('value' => 'wsnyc_categorydescriptions/condition_product|' . $code, 'label' => $label);
        }
        
        return $conditions;
    }

}
