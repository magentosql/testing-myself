<?php
include 'app/Mage.php';

Mage::app('admin');

$order = Mage::getModel('sales/order')->loadByIncrementId('100000077');

echo $order->getGiftwrapBoxType()."\n";
var_dump($order->debug());
