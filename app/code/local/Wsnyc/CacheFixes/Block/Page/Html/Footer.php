<?php

class Wsnyc_CacheFixes_Block_Page_Html_Footer extends Mage_Page_Block_Html_Footer
{
    protected function _construct()
    {
        $this->addData(
            array(
                'cache_lifetime' => false,
                'cache_key' => 'page_html_footer' . $this->getRequest()->isSecure(),
                'cache_tags' => array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG, Mage_Core_Block_Template::CACHE_GROUP)
            )
        );
    }

    public function canRenderPinterest()
    {
        // images linked by pinterest plugin are servered via connection with unsercure certifcate so we hide widget
        return !$this->getRequest()->isSecure();
    }
}