<?php

$attrCode = 'ground_only';
$update = 'WS-Bag,WS-Bathrobe,WS-Blanket,WS-BlanketQueen,WS-Blouse,WS-Bra,WS-Curtain/Drapes,WS-Dress,WS-Dress Shirt,WS-Duvet Covers,WS-Gloves,WS-Handkerchief,WS-Hats,WS-Hosiery,WS-Jacket,WS-Jeans,WS-Napkins,WS-Nightgown,WS-Pajamas,WS-Pants,WS-Pillow,WS-Pillow Case,WS-Pillow Sham,WS-Placemat,WS-Scarf,WS-Sheet,WS-Shorts,WS-Skirt,WS-Slip,WS-Socks,WS-Sweater,WS-Swimwear,WS-Table cloth,WS-Uggs,WS-Underwear,WS-Wedding Gown,WS-Blanketqueen,WS-Comforter,WS-CurtainDrapes,WS-DressShirt,WS-DuvetCovers,WS-Pillowcase,WS-PillowSham,ws-shirts,WS-Tablecloth,H-01,WHDUO,SBI-01,BH-01,DUOH01-4,DUOH01-2';

foreach(explode(',', $update) as $sku) {
    $product = Mage::getModel('catalog/product');
    $product = $product->loadByAttribute('sku', $sku);
    if($product) {
        $product->setData($attrCode, 1)->getResource()->saveAttribute($product, $attrCode);
    }
}