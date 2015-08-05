<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$blockContent = <<< EOT
<h3>All Set!</h3>
<p>Simply click "Redeem Now" to automatically<br />apply your coupon for 20% off!</p>
<p>Or copy and paste the code below:</p>
EOT;

$retailStore = Mage::getModel('core/store')->load('default','code');
$block = Mage::getModel('cms/block')->load('pageleave-modal-redeem','identifier');
$block->setIdentifier('pageleave-modal-redeem')
    ->setTitle('Modal Box for customers leaving the page - redeem button')
    ->setIsActive('1')
    ->setStores(array($retailStore->getId()))
    ->setContent($blockContent)
    ->save();

$installer->endSetup();