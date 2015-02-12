<?php

$installer = $this;

$wholesaleWebsite = Mage::getModel('core/website')->load('wholesale','code');
$retailStore = Mage::getModel('core/store')->load('default','code');

Mage::getModel('cms/page')->load('customer-service/find-a-store','identifier')
        ->setStores(array($retailStore->getId()))
        ->save();

$installer->setConfigData('advanced/modules_disable_output/Unirgy_StoreLocator', 1, 'websites', $wholesaleWebsite->getId());

Mage::app()->getConfig()->reinit();
