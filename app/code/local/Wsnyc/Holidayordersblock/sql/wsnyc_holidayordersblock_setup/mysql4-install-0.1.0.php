<?php
/**
 * Created by PhpStorm.
 * User: msyrek
 * Date: 25.10.2013
 * Time: 11:59
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$staticBlock = array(
    'title' => 'Holiday orders',
    'identifier' => 'holiday-orders',
    'content' => 'Lorem ipsum',
    'is_active' => 1,
    'stores' => array(1)
);

Mage::getModel('cms/block')->setData($staticBlock)->save();

$installer->endSetup();