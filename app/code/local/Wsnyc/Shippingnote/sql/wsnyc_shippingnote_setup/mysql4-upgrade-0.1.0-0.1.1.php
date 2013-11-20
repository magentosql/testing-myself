<?php

$notesConfig = Mage::getConfig()->getXpath('global/wsnyc_shippingnote/basic_notes');
if($notesConfig) {
    $notesConfig = $notesConfig[0];
} else {
    $notesConfig = new Varien_Object();
}
if($notesConfig->enabled) {
    /* @var $productModel Mage_Catalog_Model_Product */
    $productModel = Mage::getModel('catalog/product');
    $attributeCode = 'shipping_note';

    $notes = $notesConfig->notes;
    foreach($notes->asArray() as $note) {
        $skus = explode(',', $note['skus']);
        if($skus) {
            $products = $productModel
                ->getCollection()
                ->addAttributeToFilter('sku', array('in' => $skus))
                ->load();
            foreach($products as $product) {
                $product
                    ->setData($attributeCode, $note['noteText'])
                    ->getResource()
                    ->saveAttribute($product, $attributeCode);
            }
        }
    }
}