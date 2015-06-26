<?php
/**
 * Created by PhpStorm.
 * User: zefiryn
 * Date: 08.06.15
 * Time: 15:30
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');
$attr = array(
    'group' => 'Product Info',
    'type' => 'varchar',
    'input' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'UPC Code',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible' => true,
    'visible_on_front' => true,
    'visible_in_advanced_search' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
);
$setup->addAttribute('catalog_product', 'upc_code', $attr);

$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'upc_code');
$attribute->setStoreLabels(array('UPC Code'));
$attribute->save();

$attr = array(
    'group' => 'Product Info',
    'type' => 'varchar',
    'input' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Google Category',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible' => true,
    'visible_on_front' => true,
    'visible_in_advanced_search' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false,
);
$setup->addAttribute('catalog_product', 'google_category', $attr);

$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'google_category');
$attribute->setStoreLabels(array('Google Category'));
$attribute->save();



$installer->endSetup();