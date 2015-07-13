<?php

class Wsnyc_KeepGclid_Model_Observer extends Varien_Event_Observer
{
  public function keepParam(Varien_Event_Observer $observer)
  {
      //this might be session initialization so make sure to use correct session name for frontend
      $session = Mage::getSingleton('core/session', array('name' => Mage_Core_Controller_Front_Action::SESSION_NAMESPACE))->start();

      $param = $observer->getEvent()->getData('front')->getRequest()->getParam('gclid');
      if($param) {
          $session->setData('gclid', $param);
      }

      if($session->getData('gclid') && !$param) {
          $helper = Mage::helper('core/url');
          $currentUrl = $helper->getCurrentUrl();
          $newUrl = $helper->addRequestParam($currentUrl, array('gclid' => $session->getData('gclid')));
          Mage::app()->getResponse()->setRedirect($newUrl, 301)->sendResponse();

          return;
      }
  }
}