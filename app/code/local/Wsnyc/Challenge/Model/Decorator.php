<?php

class Wsnyc_Challenge_Model_Decorator
{
    public function getCss(Mage_Catalog_Model_Product $product)
    {
        if ("K-15" == $product->getSku()) {
            return "challenge";
        }

        return null;
    }
}