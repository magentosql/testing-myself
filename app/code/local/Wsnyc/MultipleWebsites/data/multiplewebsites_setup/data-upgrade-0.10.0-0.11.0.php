<?php
$installer = $this;
$installer->setConfigData('payment/purchaseorder/active', '1', 'websites', Mage::getModel('core/website')->load('wholesale','code')->getId());
Mage::app()->getConfig()->reinit();