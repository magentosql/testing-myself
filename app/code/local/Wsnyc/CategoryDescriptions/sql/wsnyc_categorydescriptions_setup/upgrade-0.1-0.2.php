<?php
$installer = $this;
$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('wsnyc_categorydescriptions/rule'),
    'from_date',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable' => true,
        'default' => null,
        'comment' => 'From Date'
    )
);
$connection->addColumn(
    $installer->getTable('wsnyc_categorydescriptions/rule'),
    'to_date',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable' => true,
        'default' => null,
        'comment' => 'To Date'
    )
);

$installer->endSetup();