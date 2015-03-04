<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$rule_id = Mage::getStoreConfig('promo/leavingpage/rule_id');
$rule = Mage::getModel('salesrule/rule')->load($rule_id);
$rule->setStoreLabels(array('10 Percent Off'))->save();
$installer->endSetup();