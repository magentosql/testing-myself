<?php

class Wsnyc_CategoryDescriptions_Model_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract {
    
    /**
     * Set available operator options
     * 
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['category'] = array('()', '!()');
            if (!in_array('category', $this->_arrayInputTypes)) {
                $this->_arrayInputTypes[] = 'category';
            }
        }
        return $this->_defaultOperatorInputByType;
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
        else {
            foreach($object->getFilters() as $filter) {
                if ($filter->getAttribute() == $this->getAttribute()) {
                    $is = $filter->getValue() == $this->getValue();
                    return $this->getOperator() == '==' ? $is : !$is;
                }
            }
        }
        
        return false;
    }

}
