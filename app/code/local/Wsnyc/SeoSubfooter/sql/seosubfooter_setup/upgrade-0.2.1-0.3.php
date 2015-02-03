<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup();
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'seosubfooter_blurb', array(
    'group' => 'Meta Information',
    'type' => 'varchar',
    'backend' => 'eav/entity_attribute_backend_array',
    'source' => 'seosubfooter/source_blurbs',
    'label' => 'Limit Blurbs Selection',
    'input' => 'multiselect',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => null,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => true,
    'unique' => false
));

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'seosubfooter_blurb', array(
    'group' => 'Display Settings',
    'type' => 'varchar',    
    'backend' => 'eav/entity_attribute_backend_array',
    'source' => 'seosubfooter/source_blurbs',
    'label' => 'Limit Blurbs Selection',
    'input' => 'multiselect',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'visible_on_front' => true,
    'required' => false,
    'user_defined' => true,
    'default' => null
));

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('cms/page'),
    'seosubfooter_blurb',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'default' => null,
        'comment' => 'Limit Blurbs Selection'
    )
);


if (Mage::getConfig()->getModuleConfig('Wsnyc_QuestionsAnswers')->asArray() != '') {
    $connection->addColumn(
        $installer->getTable('wsnyc_questionsanswers/category'), 'seosubfooter_blurb', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'default' => null,
        'comment' => 'Limit Blurbs Selection'
            )
    );
}

$installer->endSetup();
