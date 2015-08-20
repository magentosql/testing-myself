<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$config = unserialize(Mage::getStoreConfig('ewpagecache_advanced/injectors/list'));
$config[] = array('block_key' => 'leavingpage/modal', 'injector_key' => 'leavingpage_model');
$setup = new Mage_Core_Model_Config();
$setup->saveConfig('ewpagecache_advanced/injectors/list', serialize($config), 'default', 0);

$installer->endSetup();