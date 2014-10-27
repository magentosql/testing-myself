<?php

class Wsnyc_CategoryDescriptions_Model_Condition_Product_Attribute extends Mage_SalesRule_Model_Rule_Condition_Product_Combine {

    public function __construct() {
        parent::__construct();
        $this->setType('wsnyc_categorydescriptions/condition_product_attribute');
    }

    /**
     * Load value options
     *
     * @return Mage_SalesRule_Model_Rule_Condition_Product_Found
     */
    public function loadValueOptions() {
        $this->setValueOption(array(
            1 => Mage::helper('wsnyc_categorydescriptions')->__('FILTERED'),
            0 => Mage::helper('wsnyc_categorydescriptions')->__('NOT FILTERED')
        ));
        return $this;
    }

    public function asHtml() {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('wsnyc_categorydescriptions')->__("If a category is %s by %s of these attriubtes:", $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
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
        $all = $this->getAggregator() === 'all';
        $true = (bool) $this->getValue();
        $found = false;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if (($found && $true) || (!$true && $found)) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        }
        // not found and we're making sure it doesn't exist
        elseif (!$found && !$true) {
            return true;
        }
        return false;
    }
    
    
    /**
     * Prepare and show available attributes to select
     * 
     * @return array
     */
    public function getNewChildSelectOptions() {
        $productCondition = Mage::getModel('wsnyc_categorydescriptions/condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $pAttributes = array();
        foreach ($productAttributes as $code => $label) {
            $pAttributes[] = array('value' => 'wsnyc_categorydescriptions/condition_product|' . $code, 'label' => $label);
        }
        
        $conditions = array(
            array('value'=>'', 'label'=>Mage::helper('rule')->__('Please choose a condition to add...')),
            array('label' => Mage::helper('catalog')->__('Attributes'), 'value' => $pAttributes)
        );
        return $conditions;
    }

}
