<?php

$installer = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$installer->startSetup();

$installer->addAttribute('order', 'signature_required', array('type' => 'int','label' => 'Is signature required?'));
$installer->addAttribute('quote', 'signature_required', array('type' => 'int','label' => 'Is signature required?'));

$installer->endSetup();