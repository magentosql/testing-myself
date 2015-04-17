<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge', 'identifier');

$pageContent = $page->getContent();
//link to facebook page
$pageContent = str_replace('Join us On Facebook &amp; Instagram', 'Join us On <a href="https://www.facebook.com/thelaundressnyc">Facebook</a> &amp; Instagram', $pageContent);

//add link to bundle product
$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', 'K-15');
$pageContent = str_replace('<img src="{{media url="wysiwyg/30daychallenge/products.png"}}" alt="Promotion products" width="883" height="410" />', '<a href="'.$_product->getProductUrl().'"><img src="{{media url="wysiwyg/30daychallenge/products.png"}}" alt="Promotion products" width="883" height="410" /></a>', $pageContent);

//add cta button
$categoryLink = '<a class="button" href="{{store url="for-the-home/home-cleaning}}"><span><span>Shop Now</span></span></a>';
$pageContent = str_replace('<strong>30DAYCLEAN</strong></span></div>', '<strong>30DAYCLEAN</strong></span>'.$categoryLink.'</div>', $pageContent);

//add cta button
$productLink = '<a class="button" href="'.$_product->getProductUrl().'"><span><span>Shop Now</span></span></a>';
$pageContent = str_replace('$80</span>', '$80</span>' . $productLink, $pageContent);

$cmsPageData = array(
    'content' => $pageContent
);
$page->addData($cmsPageData)->save();


$installer->endSetup();