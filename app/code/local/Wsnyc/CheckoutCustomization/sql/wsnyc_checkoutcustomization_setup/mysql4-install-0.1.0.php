<?php
/**
 * Created by PhpStorm.
 * User: msyrek
 * Date: 04.12.2013
 * Time: 08:25
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('sales_flat_order')} ADD `onestepcheckout_giftwrap_type` text NULL");

$installer->endSetup();