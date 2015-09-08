<?php

class Wsnyc_AnalyticsYoutube_Block_Tracking extends Mage_Core_Block_Template
{
  protected function _beforeToHtml()
  {
    if(!Mage::getStoreConfig('wsnyc_video_analytics/youtube/enabled') ||
      !Mage::getStoreConfig('google/analytics/account') ||
      !Mage::getStoreConfig('google/analytics/active')){
      $this->setTemplate(null);
    }
    return parent::_beforeToHtml();
  }
}