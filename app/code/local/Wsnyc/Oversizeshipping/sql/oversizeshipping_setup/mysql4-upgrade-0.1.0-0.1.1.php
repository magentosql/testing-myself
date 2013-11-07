<?php

$attrCode = 'oversize_shipping_price';
$update = array(
    'A-039' => '9.25',
    'A-074' => '9.25',
    'A-056' => '25',
    'A-051' => '30',
    'A-051a' => '30',
    'A-052' => '25',
    'A-053' => '30',
    'A-057' => '25'
);

foreach($update as $sku => $price) {
    $product = Mage::getModel('catalog/product');
    $product = $product->loadByAttribute('sku', $sku);
    if($product) {
        $product->setData($attrCode, $price)->getResource()->saveAttribute($product, $attrCode);
    }
}