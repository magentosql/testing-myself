<?php

include '../app/Mage.php';
Mage::app();

if (($handle = fopen("tax.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $url_key = $data[0];
        $product = Mage::getModel('catalog/product')->loadByAttribute('url_key', $url_key);
        if (!$product) {
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $url_key);
        }
        if ($product) {
            $product->setGoogleCategory($data[1]);
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                            array($product->getId()),
                            array('google_category'=>$data[1]),
                            0);
            echo $url_key.'::'.$product->getGoogleCategory()."\n";
        }
        else {
            echo "Missing {$url_key}\n";
        }
    }
    fclose($handle);
}
