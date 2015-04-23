<?php
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('sku', array('in' => array('sh-01')));
foreach($collection as $product) {
    $product->setIsChallenge(true)->save();
}
$installer->endSetup();