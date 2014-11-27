<?php
/**
 * @var Zefir_Dealers_Model_Resource_Setup $installer
 */
$installer = $this;  
$installer->startSetup();
$installer->getConnection()->dropTable($installer->getTable('seosubfooter/blurb'));
$table = $installer->getConnection()->newTable($installer->getTable('seosubfooter/blurb'))
                  ->addColumn('blurb_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true,'nullable' => false,'primary' => true,'identity' => true ), 'Dealer ID')
                  ->addColumn('title',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Blurb Title')
                  ->addColumn('url',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Blurb Link')
                  ->addColumn('blurb_content',Varien_Db_Ddl_Table::TYPE_TEXT, null, array('nullable' => false), 'Blurb Content')
                  ->addColumn('status',Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array('nullable'  => false,'default' => '1'), 'Status');
$installer->getConnection()->createTable($table);
$installer->endSetup();