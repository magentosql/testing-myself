<?php
$installer = $this;
$wholesaleWebsite = Mage::getModel('core/website')->load('wholesale','code');
$installer->setConfigData('web/unsecure/base_url', 'http://wholesale.thelaundress.com/', 'websites', $wholesaleWebsite->getId());
$installer->setConfigData('web/secure/base_url', 'https://wholesale.thelaundress.com/', 'websites', $wholesaleWebsite->getId());
$installer->setConfigData('web/default/cms_home_page', 'home', 'websites', $wholesaleWebsite->getId());
Mage::app()->getConfig()->reinit();