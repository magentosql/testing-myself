<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$rule_id = Mage::getStoreConfig('promo/leavingpage/rule_id', 0);
$rule = Mage::getModel('salesrule/rule')->load($rule_id);
$rule->setDiscountAmount(10.0000)->save();
$rule->setStoreLabels(array('10 Percent Off'))->save();

$block = Mage::getModel('cms/block')->load('pageleave-modal','identifier');
$blockContent = str_replace('20%', '10%', $block->getContent());
$block->setContent($blockContent)->save();

$block = Mage::getModel('cms/block')->load('pageleave-modal-redeem','identifier');
$blockContent = str_replace('20%', '10%', $block->getContent());
$block->setContent($blockContent)->save();

$installer->endSetup();