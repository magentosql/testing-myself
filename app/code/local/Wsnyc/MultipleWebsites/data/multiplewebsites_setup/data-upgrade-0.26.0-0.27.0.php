<?php

$installer = $this;

$retailStore = Mage::getModel('core/store')->load('default','code');

$cmsPage = Mage::getModel('cms/page')->getCollection()
        ->addFieldToFilter('identifier', array('eq' => 'customer-service/terms'))
        ->addStoreFilter($retailStore)
        ->load()
        ->getFirstItem();
        
$cmsPage->setStores(array($retailStore->getId()))
        ->save();