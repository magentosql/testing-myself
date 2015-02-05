<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'seosubfooter_text', array(
    'group' => 'Meta Information',
    'type' => 'text',
    'backend' => '',
    'source' => '',
    'label' => 'SEO Subfooter Text',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => null,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => false,
    'unique' => false
));
$setup->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'seosubfooter_text', 'is_wysiwyg_enabled', 1);
$setup->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'seosubfooter_text', 'is_html_allowed_on_front', 1);

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'seosubfooter_text', array(
    'group' => 'Display Settings',
    'type' => 'text',
    'backend' => '',
    'source' => '',
    'label' => 'SEO Subfooter Text',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'visible_on_front' => true,
    'required' => false,
    'user_defined' => true,
    'default' => null
));
$setup->updateAttribute(Mage_Catalog_Model_Category::ENTITY, 'seosubfooter_text', 'is_wysiwyg_enabled', 1);
$setup->updateAttribute(Mage_Catalog_Model_Category::ENTITY, 'seosubfooter_text', 'is_html_allowed_on_front', 1);

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
