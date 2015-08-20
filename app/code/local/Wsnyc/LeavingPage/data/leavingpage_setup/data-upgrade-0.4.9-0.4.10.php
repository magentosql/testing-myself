<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$rule_id = Mage::getStoreConfig('promo/leavingpage/rule_id', 0);
$rule = Mage::getModel('salesrule/rule')->load($rule_id);
$rule->setDiscountAmount(20.0000)->save();
$rule->setStoreLabels(array('20 Percent Off'))->save();

$blockContent = <<< EOT
<h3>Stay a while longer...</h3>
<p>We promise you'll love our products.<br />Shop with 20% off your order now!</p>
EOT;

Mage::getModel('cms/block')->load('pageleave-modal','identifier')
    ->setContent($blockContent)
    ->save();

$installer->endSetup();