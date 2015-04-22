<?php
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('sku', array('in' => array(
                    'A-028', 'Signature-Detergent', 'Bleach-Alternative', 'Scented-Vinegar', 'Cleaning-Concentrate', 'Stain-Solution'))
                );
foreach($collection as $product) {
    $product->setIsChallenge(true)->save();
}
$installer->endSetup();