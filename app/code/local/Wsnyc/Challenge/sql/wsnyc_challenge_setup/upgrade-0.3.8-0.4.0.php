<?php
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_product', 'is_challenge', array(
    'group' => 'General',
    'sort_order' => 3,
    'type' => 'int',
    'label' => 'Show Challenge Description',
    'source' => 'eav/entity_attribute_source_boolean',
    'input' => 'select',
    'frontend_class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'unique' => false
));

$installer->endSetup();