<?php
$installer = Mage::getModel('customer/entity_setup', 'core_write');
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('customer/customer_group'), 'group_type', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 5,
            'nullable' => false,
            'comment' => 'Customer Group Type'
        )
    );
$installer->getConnection()->addColumn($installer->getTable('customer/customer_group'), 'price_multiplier', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 5,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '1',
            'comment' => 'Price Multiplier'
        )
    );
$installer->endSetup();