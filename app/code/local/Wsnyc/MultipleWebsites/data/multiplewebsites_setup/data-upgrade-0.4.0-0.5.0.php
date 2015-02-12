<?php
$wholesaleWebsiteId = Mage::getModel('core/website')->load('wholesale','code')->getId();
$defaultWebsiteId = Mage::getModel('core/website')->load('base','code')->getId();
$productsCollection = Mage::getModel('catalog/product')->getCollection();
foreach($productsCollection as $product) {
    try {
        $product->setWebsiteIds(array($defaultWebsiteId, $wholesaleWebsiteId))->save();
    } catch (Exception $e) {
        Mage::log($e->getMessage());
    }
}