<?php

class Wsnyc_CategoryDescriptions_Model_Condition_Combine extends Mage_Rule_Model_Condition_Combine {

    public function __construct() {
        parent::__construct();
        $this->setType('wsnyc_categorydescriptions/condition_combine');
    }

    /**
     * Set options for "If ALL/ANY of these conditions are TRUE/FALSE" field
     * 
     * @return array
     */
    public function getNewChildSelectOptions() {
        
        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), array(
            array('value' => 'wsnyc_categorydescriptions/condition_product_attribute', 'label' => Mage::helper('salesrule')->__('Filters combination')),
            array('value' => 'wsnyc_categorydescriptions/condition_combine', 'label' => Mage::helper('salesrule')->__('Conditions combination')),           
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('category_description_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * Render select with available options to choose from
     * 
     * @return Varien_Data_Form
     */
    public function getNewChildElement() {
        return $this->getForm()->addField($this->getPrefix() . '__' . $this->getId() . '__new_child', 'select', array(
                    'name' => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][new_child]',
                    'values' => $this->getNewChildSelectOptions(),
                    'value_name' => $this->getNewChildName(),
                ))->setRenderer(Mage::getBlockSingleton('wsnyc_categorydescriptions/renderer_newchild'));
    }

    /**
     * Recursively render all conditions
     * 
     * @return string
     */
    public function asHtmlRecursive() {
        $html = $this->asHtml() . '<ul id="' . $this->getPrefix() . '__' . $this->getId() . '__children" class="rule-param-children">';
        foreach ($this->getConditions() as $cond) {
            $html .= '<li>' . $cond->asHtmlRecursive() . '</li>';
        }
        $html .= '<li>' . $this->getNewChildElement()->getHtml() . '</li></ul>';
        return $html;
    }
    
    public function validate(Varien_Object $object) {
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool) $this->getValue();

        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validate($object);

            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }

}
