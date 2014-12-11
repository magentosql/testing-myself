<?php

$installer = $this;
$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('cms/page'),
    'seosubfooter_link',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => null,
        'comment' => 'SEO Landing Page'
    )
);

$installer->endSetup();
