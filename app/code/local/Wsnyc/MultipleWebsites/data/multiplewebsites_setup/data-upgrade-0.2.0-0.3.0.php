<?php
$cmsBlocks = Mage::getModel('cms/block')->getCollection();
$cmsPages = Mage::getModel('cms/page')->getCollection();
foreach($cmsBlocks as $cmsBlock) {
    try {
        $cmsBlock->setStores(array(0))->save();
    } catch (Exception $e) {
        Mage::log($e->getMessage());
    }
}
foreach($cmsPages as $cmsPage) {
    try {
        $cmsPage->setStores(array(0))->save();
    } catch (Exception $e) {
        Mage::log($e->getMessage());
    }
}