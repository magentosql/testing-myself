<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$rule_id = Mage::getStoreConfig('wsnyc/leavingpage/rule_id', 0);
$rule = Mage::getModel('salesrule/rule')->load($rule_id);
$rule->setUsesPerCoupon(1)->save();
$installer->endSetup();