<?php

$installer = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$installer->startSetup();

$installer->addAttribute('order', 'onestepcheckout_laundress_comment', array('type' => 'varchar','label' => 'Comment for Laundress'));

$installer->endSetup();