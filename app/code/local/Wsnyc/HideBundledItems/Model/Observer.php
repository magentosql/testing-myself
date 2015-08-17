<?php

class Wsnyc_HideBundledItems_Model_Observer extends Varien_Event_Observer
{
  /**
   * @param \Varien_Event_Observer $observer
   * @return $this
   */
  public function addHandle(Varien_Event_Observer $observer)
  {
    $product = Mage::registry('current_product');
    if($product) {
      if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
        if($product->getData('hide_bundle_items')) {
          $layout = $observer->getEvent()->getLayout();
          $block = $layout->getBlock('head');
          if($block){
            $block->addItem('skin_css','css/wsnyc/hidebundleditems.css');
          }
        }
      }
    }
    return $this;
  }
}