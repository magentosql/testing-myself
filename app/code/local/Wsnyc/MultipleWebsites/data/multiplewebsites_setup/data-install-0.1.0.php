<?php

$website = Mage::getModel('core/website');
$website->setCode('wholesale')
        ->setName('The Laundress Wholesale')
        ->save();

$storeGroup = Mage::getModel('core/store_group');
$storeGroup->setWebsiteId($website->getId())
        ->setName('The Laundress Wholesale Store')
        ->setRootCategoryId(2)
        ->save();

$store = Mage::getModel('core/store');
$store->setCode('wholesaledefault')
        ->setWebsiteId($storeGroup->getWebsiteId())
        ->setGroupId($storeGroup->getId())
        ->setName('Default Store View')
        ->setIsActive(1)
        ->save();
