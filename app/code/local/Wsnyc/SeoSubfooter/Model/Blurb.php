<?php

class Wsnyc_SeoSubfooter_Model_Blurb extends Varien_Object {

    public function getFilteredContent() {
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        return $processor->filter($this->getBlurbContent());
    }

}
