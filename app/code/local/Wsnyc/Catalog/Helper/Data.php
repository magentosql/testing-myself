<?php

class Wsnyc_Catalog_Helper_Data extends Mage_Core_Helper_Abstract {

    public function parseTabDetail($content) {
        if(empty($content)) {
            return null;
        }

        $lines = explode("\n", $content);
        foreach($lines AS $no => $line) {
            if(strlen($line) < 60) {
                $lines[$no] = "<h4>" . $line . "</h4>";
            }
        }
        $html = implode("\n", $lines);

        return $html;
    }

    public function getAttributeStoreLabel($attribute) {
        return Mage::registry('current_product')->getResource()->getAttribute($attribute)->getStoreLabel();
    }

    public function getBlockLabel($block_id) {
        return Mage::getModel('cms/block')->load($block_id)->getTitle();
    }
}