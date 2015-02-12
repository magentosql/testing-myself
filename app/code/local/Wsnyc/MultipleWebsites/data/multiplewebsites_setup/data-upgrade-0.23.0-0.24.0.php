<?php

$installer = $this;

$wholesaleWebsite = Mage::getModel('core/website')->load('wholesale','code');

$installer->setConfigData('carriers/fedex/allowed_methods', 'FEDEX_GROUND', 'websites', $wholesaleWebsite->getId());

Mage::app()->getConfig()->reinit();