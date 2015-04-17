<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-sample-request', 'identifier');

$pageContent = $page->getContent();

$pageContent = str_replace('<label for="newsletter">Subscribe me to the newsletter</label>', '<label for="newsletter">Subscribe me to the newsletter</label>
<span>Be the first to hear about exclusive promotions, discounts, insider tips, and news!</span>', $pageContent);

$cmsPageData = array(
    'content' => $pageContent
);
$page->addData($cmsPageData)->save();


$installer->endSetup();