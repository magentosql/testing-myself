<?php

class Wsnyc_ReviewAlert_Model_Observer extends Varien_Event_Observer
{
  const XML_PATH_EMAIL_ENABLED = 'catalog/review/receive_email';
  const XML_PATH_ADMIN_EMAIL = 'catalog/review/admin_email';
  const XML_PATH_RECIPIENT_NAME = 'trans_email/ident_%s/name';
  const XML_PATH_RECIPIENT_EMAIL = 'trans_email/ident_%s/email';

  const XML_PATH_EMAIL_TEMPLATE_PENDING = 'catalog/review/template_pending';


  public function sendEmail(Varien_Event_Observer $observer)
  {
    if(!Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED)) {
      return;
    }

    $identity = Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL);
    if(!$identity) {
      return;
    }

    $helper = Mage::helper('wsnyc_reviewalert');

    $recipientName = Mage::getStoreConfig($helper->__(self::XML_PATH_RECIPIENT_NAME, $identity));
    $recipientEmail = Mage::getStoreConfig($helper->__(self::XML_PATH_RECIPIENT_EMAIL, $identity));
    if(!$recipientEmail) {
      return;
    }

    $review = $observer->getEvent()->getDataObject()->getData();
    $review = array_filter($review, array($this, '_notArray'));
    if(!array_key_exists('status_id', $review)) {
      return;
    }

    $emailTemplateVariables['customer_name'] = $review['nickname'];
    if($review['customer_id']) {
      $customer = Mage::getModel('customer/customer')->load($review['customer_id']);
      $emailTemplateVariables['customer_name'] = $customer->getFirstname() . ' ' . $customer->getLastname();
    }

    $dateModel = Mage::getModel('core/date');

    $emailTemplateVariables['product_name'] = Mage::getModel('catalog/product')->load($review['entity_pk_value'])->getName();
    $emailTemplateVariables['recipient_name'] = $recipientName;
    $emailTemplateVariables['recipient_email'] = $recipientEmail;
    $emailTemplateVariables['review_time'] = $dateModel->date('h:i A', $review['created_at']) . ' on '
      . $dateModel->date('F j, Y', $review['created_at']);
    $emailTemplateVariables['review_admin_url'] = Mage::helper("adminhtml")->getUrl
    ("adminhtml/catalog_product_review/edit", array('id' => $review['review_id'], 'ret' => 'pending'));
    $emailTemplateVariables = array_merge($review, $emailTemplateVariables);

    $emailTemplate = Mage::getModel('core/email_template');

    $templatePath = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_PENDING);


    $emailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getStoreId()))
      ->sendTransactional(
        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_PENDING),
        Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
        $recipientEmail,
        $recipientName,
        $emailTemplateVariables
    );
    return $this;
  }

  protected function _notArray($element)
  {
    return !is_array($element);
  }
}