<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$connection = $this->getConnection();
$connection->addColumn($installer->getTable('sales/quote'), 'third_party_shipping_account', array(
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => 255,
        'NULLABLE'  => true,
        'COMMENT'   => 'Third Party Shipping Account Number'
    ));
$connection->addColumn($installer->getTable('sales/order'), 'third_party_shipping_account', array(
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => 255,
        'NULLABLE'  => true,
        'COMMENT'   => 'Third Party Shipping Account Number'
    ));
$installer->endSetup();
