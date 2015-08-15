<?php
/**
 * Created by PhpStorm.
 * User: shawn
 * Date: 8/14/15
 * Time: 4:19 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'hide_bundle_items', array(
  'group'             => 'Design',
  'type'              => 'int',
  'backend'           => '',
  'frontend'          => '',
  'label'             => 'Hide Bundled Products',
  'input'             => 'select',
  'class'             => '',
  'source'            => 'eav/entity_attribute_source_boolean',
  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
  'required'          => false,
  'user_defined'      => false,
  'default'           => 0,
  'searchable'        => false,
  'filterable'        => false,
  'comparable'        => false,
  'visible_on_front'  => false,
  'unique'            => false,
  'is_configurable'   => false
));
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'hide_bundle_items', 'is_visible', 1);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'hide_bundle_items', 'apply_to', Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);


$installer->endSetup();