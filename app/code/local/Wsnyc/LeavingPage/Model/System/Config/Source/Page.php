<?php

/**
 * Used in creating option for 'Allowed pages' config value
 *
 * Class Wsnyc_LeavingPage_Model_System_Config_Source_Page
 */
class Wsnyc_LeavingPage_Model_System_Config_Source_Page {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->toArray() as $key => $label) {
            $options[] = array('value' => $key, 'label' => $label,
            );
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'cms-index-index' => Mage::helper('leavingpage')->__('Home page'),
            'cms-index' => Mage::helper('leavingpage')->__('CMS pages'),
            'checkout-cart' => Mage::helper('leavingpage')->__('Shopping Cart'),
            'customer-account' => Mage::helper('leavingpage')->__('Customer Account'),
            'catalog-category' => Mage::helper('leavingpage')->__('Catalog Category'),
            'catalog-product' => Mage::helper('leavingpage')->__('Catalog Product'),
            'asklaundress-index' => Mage::helper('leavingpage')->__('Ask The Laundress'),
        );
    }
}