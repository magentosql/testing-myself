<?php

$installer = $this;

$blockContent = <<< EOT
<h3>We're sorry to see you go!</h3>
<p>If you decide to stay with us a little longer, we'd like to offer you a 10% promotion - just use the discount code PROMO10 when checking out!</p>
EOT;

$wholesaleWebsite = Mage::getModel('core/website')->load('wholesale','code');
$retailStore = Mage::getModel('core/store')->load('default','code');

$block = Mage::getModel('cms/block')->load('pageleave-modal','identifier');
if($block->isObjectNew()) {
    $block->setIdentifier('pageleave-modal')
            ->setTitle('Modal Box for customers leaving the page')
            ->setIsActive('1')
            ->setStores(array($retailStore->getId()));
} 
$block->setContent($blockContent)
        ->save();

if($wholesaleWebsite->getId()) {
    $installer->setConfigData('advanced/modules_disable_output/Wsnyc_LeavingPage', 1, 'websites', $wholesaleWebsite->getId());
}