<?php
$installer = $this;
$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();

$connection->dropTable($installer->getTable('wsnyc_categorydescriptions/rule'));
$ruleTable = $connection->newTable($installer->getTable('wsnyc_categorydescriptions/rule'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'identity' => true
                ), 'Rule ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array('nullable' => false), 'Rule Name')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullable' => false), 'Active Flag')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array('nullable' => false), 'Conditions')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => true), 'Sort Order')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array('nullable' => false), 'Description')
    ->setComment('Category Description Rules Table');
    
$connection->createTable($ruleTable);

$connection->dropTable($installer->getTable('wsnyc_categorydescriptions/store'));
$storeTable = $connection->newTable($installer->getTable('wsnyc_categorydescriptions/store'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Rule Id'
        )
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ),
        'Store Id'
    )
    ->addIndex(
        $installer->getIdxName('wsnyc_categorydescriptions/store', array('rule_id')),
        array('rule_id')
    )
    ->addIndex(
        $installer->getIdxName('wsnyc_categorydescriptions/store', array('store_id')),
        array('store_id')
    )
    ->addForeignKey($installer->getFkName('wsnyc_categorydescriptions/store', 'rule_id', 'wsnyc_categorydescriptions/rule', 'rule_id'),
        'rule_id', $installer->getTable('wsnyc_categorydescriptions/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey($installer->getFkName('wsnyc_categorydescriptions/store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Description Rules To Stores Relations');

$connection->createTable($storeTable);

$installer->endSetup();