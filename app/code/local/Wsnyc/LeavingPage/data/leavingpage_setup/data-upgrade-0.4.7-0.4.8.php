<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$blockContent = <<< EOT
<h3>Stay a while longer...</h3>
<p>We promise you'll love our products.<br />Shop with 10% off your order now!</p>
EOT;

Mage::getModel('cms/block')->load('pageleave-modal','identifier')
    ->setContent($blockContent)
    ->save();

$installer->endSetup();