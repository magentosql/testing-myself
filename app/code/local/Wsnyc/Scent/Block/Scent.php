<?php

class Wsnyc_Scent_Block_Scent extends Mage_Catalog_Block_Product_View_Attributes {
    
    /**
     * Find cms block with description for the given product scent
     * 
     * @param Mage_Catalog_Model_Product $_product
     * @return string
     */
    public function getScentBlock() {
        $_product = $this->getProduct();
        $scent = str_replace(array(' ', ':'), '_', $_product->getAttributeText('scent'));
        $identifier = strtolower('block_attribute_scent_' . $scent);
        var_dump($identifier);
        $cmsBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');
        
        $html = '';
        if ($cmsBlock->getId() && $cmsBlock->getIsActive()) {
            $helper = Mage::helper('cms');
            $processor = $helper->getBlockTemplateProcessor();
            $html = $processor->filter($cmsBlock->getContent());
        }
        
        return $html;
    }
}