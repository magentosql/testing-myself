<?php
$installer = $this;
$wholesaleWebsite = Mage::getModel('core/website')->load('wholesale','code');
$installer->setConfigData('sales/minimum_order/active', 1, 'websites', $wholesaleWebsite->getId());
$installer->setConfigData('sales/minimum_order/amount', '350', 'websites', $wholesaleWebsite->getId());
Mage::app()->getConfig()->reinit();