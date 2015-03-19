<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$installer->setConfigData('promo/leavingpage/only_new', 1, 'default', 0);
$installer->endSetup();