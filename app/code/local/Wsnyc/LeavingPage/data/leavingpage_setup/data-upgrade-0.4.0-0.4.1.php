<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$rule_id = Mage::getStoreConfig('promo/leavingpage/rule_id');
$rule = Mage::getModel('salesrule/rule')->load($rule_id);
$rule->setDiscountAmount(10.0000)->save();
$installer->endSetup();