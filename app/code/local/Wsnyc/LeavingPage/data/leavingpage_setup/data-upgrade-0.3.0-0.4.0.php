<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$rule_id = Mage::getStoreConfig('wsnyc/leavingpage/rule_id', 0);
$installer->setConfigData('promo/leavingpage/rule_id', $rule_id, 'default', 0);
$installer->setConfigData('promo/leavingpage/active', 1, 'default', 0);
$installer->setConfigData('promo/leavingpage/prefix', 'SHOPNOW-', 'default', 0);
$installer->setConfigData('promo/leavingpage/length', 3, 'default', 0);
$installer->endSetup();