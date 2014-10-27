<?php

class Wsnyc_CategoryDescriptions_Model_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract {

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
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object) {
        $product = false;
        if ($object->getProduct() instanceof Mage_Catalog_Model_Product) {
            $product = $object->getProduct();
        } else {
            $product = Mage::getModel('catalog/product')
                    ->load($object->getProductId());
        }
        return parent::validate($product);
    }

}
