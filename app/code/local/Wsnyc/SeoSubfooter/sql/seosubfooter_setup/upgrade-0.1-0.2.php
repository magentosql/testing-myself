<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup();
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'seosubfooter_show', array(
    'group' => 'Meta Information',
    'type' => 'int',    
    'backend' => '',
    'source' => 'eav/entity_attribute_source_boolean',    
    'label' => 'Show SEO Subfooter',
    'input' => 'select',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '0',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => true,
    'unique' => false,    
));

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'seosubfooter_show', array(
    'group' => 'Display Settings',
    'type' => 'int',    
    'backend' => '',
    'source' => 'eav/entity_attribute_source_boolean',
    'label' => 'Show SEO Subfooter',
    'input' => 'select',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'visible_on_front' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '0',    
));

/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection * 
 */
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('cms/page'),
    'seosubfooter_show',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => null,
        'comment' => 'Show SEO Footer'
    )
);

if (Mage::getConfig()->getModuleConfig('Wsnyc_QuestionsAnswers')->asArray() != '') {
    $connection->addColumn(
            $installer->getTable('wsnyc_questionsanswers/category'), 'seosubfooter_show', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default' => null,
        'comment' => 'Show SEO Footer'
            )
    );
}


$installer->endSetup();
