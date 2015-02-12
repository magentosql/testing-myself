<?php
$installer = $this;
$installer->setConfigData('design/theme/template', 'wholesale', 'websites', Mage::getModel('core/website')->load('wholesale','code')->getId());
Mage::app()->getConfig()->reinit();