<?php
$installer = $this;

$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();

$connection->dropTable($installer->getTable('promomodals/modal'));
$modalsTable = $connection->newTable($installer->getTable('promomodals/modal'))
    ->addColumn('modal_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
                'identity' => true
                ), 'Modal ID')
    ->addColumn('modal_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array('nullable' => false), 'Modal Name')
    ->addColumn('modal_is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullable' => false), 'Active Flag')
    ->addColumn('modal_link_name', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array('nullable' => false), 'Header Link')    
    ->addColumn('modal_description', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array('nullable' => false), 'Description')    
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false), 'Rule ID')    
    ->setComment('Promo modals table');
    
$connection->createTable($modalsTable);