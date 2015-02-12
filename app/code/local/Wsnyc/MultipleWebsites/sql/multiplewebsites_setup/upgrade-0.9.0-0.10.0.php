<?php
$installer = Mage::getModel('eav/entity_setup', 'core_write');
$installer->startSetup();
$installer->addAttribute('catalog_product', 'qty_multiplier', array(
    'group'             => 'General',
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Wholesale Quantity Multiplier',
    'note'              => 'On the Wholesale website, products can be bought only in quantities that are multipliers of the number provided.',
    'input'             => 'text',
    'class'             => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '1',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'is_configurable'   => false
));
$installer->endSetup();