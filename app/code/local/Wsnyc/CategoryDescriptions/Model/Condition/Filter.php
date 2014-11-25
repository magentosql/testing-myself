<?php

class Wsnyc_CategoryDescriptions_Model_Condition_Filter extends Mage_Rule_Model_Condition_Product_Abstract {
    
    /**
     * Set available operator options
     * 
     * @return array
     */
    public function getDefaultOperatorInputByType() {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = array(
                'string' => array('==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'),
                'numeric' => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),
                'date' => array('==', '>=', '<='),
                'select' => array('{}', '!{}', '()', '!()'),
                'multiselect' => array('{}', '!{}', '()', '!()'),
                'grid' => array('()', '!()'),
                'category' => array('()', '!()')
            );
            $this->_arrayInputTypes = array('multiselect', 'grid');
        }
        return $this->_defaultOperatorInputByType;
    }

    public function getDefaultOperatorOptions() {
        if (null === $this->_defaultOperatorOptions) {
            $this->_defaultOperatorOptions = array(
                '==' => Mage::helper('rule')->__('is'),
                '!=' => Mage::helper('rule')->__('is not'),
                '>=' => Mage::helper('rule')->__('equals or greater than'),
                '<=' => Mage::helper('rule')->__('equals or less than'),
                '>' => Mage::helper('rule')->__('greater than'),
                '<' => Mage::helper('rule')->__('less than'),
                '{}' => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),
                '()' => Mage::helper('rule')->__('is one of'),
                '!()' => Mage::helper('rule')->__('is not one of')
            );
        }
        return $this->_defaultOperatorOptions;
    }

    /**
     * Load attribute options
     *
     * @return Wsnyc_CategoryDescriptions_Model_Condition_Product
     */
    public function loadAttributeOptions() {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
                ->loadAllAttributes()
                ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            /**
             * @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute 
             */            
            if (!$attribute->getIsFilterable()) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        asort($attributes);
        $attributes['category_ids'] = Mage::helper('catalogrule')->__('Category');

        $this->setAttributeOption($attributes);

        return $this;
    }
    
    public function getValueElementType() {
        if ($this->getAttribute() === 'attribute_set_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'multiselect';
                
            case 'boolean':
                return 'multiselect';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }
    
    /**
     * Prepares values options to be used as select options or hashed array
     * Result is stored in following keys:
     *  'value_select_options' - normal select array: array(array('value' => $value, 'label' => $label), ...)
     *  'value_option' - hashed array: array($value => $label, ...),
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    protected function _prepareValueOptions() {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                $selectOptions = $attributeObject->getSource()->getAllOptions(false);
            }
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Validate Attribute Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object) {

        if ($this->getAttribute() == 'category_ids') {
            $oneOf = in_array($object->getCategoryId(), explode(',', $this->getValue()));
            return $this->getOperator() == '()' ? $oneOf : !$oneOf;
        }
        
        foreach($object->getFilters() as $attributeCode => $values) {
            if ($attributeCode != $this->getAttribute()) {
                continue;
            }
            switch ($this->getOperator()) {
                case '()':
                case '!()':
                    $ruleValue = !is_array($this->getValue()) ? explode(',',$this->getValue()) : $this->getValue();
                    $result = count(array_intersect($ruleValue, $values)) > 0;
                    return $this->getOperator() == '()' ? $result : !$result;

                case '{}':
                case '!{}':
                    $ruleValue = !is_array($this->getValue()) ? explode(',',$this->getValue()) : $this->getValue();
                    $result = count(array_diff($ruleValue, $values)) == 0;
                    return $this->getOperator() == '{}' ? $result : !$result;
            }
        }
        
        
        return false;
    }    
}
