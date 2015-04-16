<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-sample-request', 'identifier');

$pageContent = $page->getContent();
$cmsPageData = array(
    'content' => str_replace("Products</h1>", "Product</h1>", $pageContent)
);
$page->addData($cmsPageData)->save();

$successContent = <<< EOT
<div class="main-banner">
    <img src="{{media url="wysiwyg/30daychallenge/request-banner.png"}}" alt="30 Day Clean Home Challenge" width="960" height="313"/>
</div>
<h1>Your Request Has Been Received</h1>
<hr class="clean-challenge-separator"/>
<div class="main-description">
    <p>We appreciate your interest in The Laundress. Your sample will be on the way shortly.</p>
</div>
<div class="details">
    <span class="call">Please call us at (212) 209-0074 with any questions</span>
</div>
EOT;


$successPageData = array(
    'title' => 'Challenge Sample Request Sucess Page',
    'root_template' => 'one_column',
    'identifier' => '30-day-clean-home-challenge-sample-request-success',
    'stores' => array(0),//available for all store views
    'content' => $successContent,
    'custom_layout_update_xml' => '<remove name="breadcrumbs" /><reference name="head"><action method="addItem"><type>skin_css</type><file>css/clean-challenge.css</file></action><block type="wsnyc_fbpixel/pixel" name="facbook_conversion_pixel" template="wsnyc/fbpixel/conversion.phtml" /></reference>'
);

$successPage = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-sample-request-success', 'identifier');
$successPage->addData($successPageData )->save();

$installer->endSetup();