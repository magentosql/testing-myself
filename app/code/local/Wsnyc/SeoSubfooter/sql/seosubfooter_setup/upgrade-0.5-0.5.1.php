<?php

$installer = $this;
$installer->startSetup();

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('cms/page'),
    'seosubfooter_text',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '2M',
        'nullable' => true,
        'default' => null,
        'comment' => 'SEO Subfooter Text'
    )
);


if (Mage::getConfig()->getModuleConfig('Wsnyc_QuestionsAnswers')->asArray() != '') {
    $connection->addColumn(
        $installer->getTable('wsnyc_questionsanswers/category'), 'seosubfooter_text', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => '2M',
            'nullable' => true,
            'default' => null,
            'comment' => 'SEO Subfooter Text'
        )
    );
}

$installer->endSetup();
