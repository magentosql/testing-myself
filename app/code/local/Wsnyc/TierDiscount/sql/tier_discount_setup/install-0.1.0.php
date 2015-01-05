<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
 
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('salesrule/rule'),
    'tier_discount',
    'text'
);

$installer->endSetup();