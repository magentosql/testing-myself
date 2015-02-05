<?php
/**
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection
 */
$connection = $installer->getConnection();
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),
    'capacity_send_status',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable' => true,
        'default' => 0,
        'comment' => 'Capacity Send Flag'
    )
);
$installer->getConnection()->dropColumn($installer->getTable('sales/shipment'),'capacity_send_status');

$installer->endSetup();