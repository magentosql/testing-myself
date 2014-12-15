<?php
$installer = $this;

$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('promomodals/modal'),
    'modal_sort_order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => 0,
        'comment' => 'Sorting Order'
    )
);