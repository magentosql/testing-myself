<?php

$installer = $this;

$cmsPage = Mage::getModel('cms/page')->getCollection()
        ->addFieldToFilter('identifier', array('eq' => 'customer-service/terms'))
        ->addStoreFilter(Mage::getModel('core/store')->load('default','code'))
        ->load()
        ->getFirstItem();
        
$cmsPage->setStores(array($retailStore->getId()))
        ->save();