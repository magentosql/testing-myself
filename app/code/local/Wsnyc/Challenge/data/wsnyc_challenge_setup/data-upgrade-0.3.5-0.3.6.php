<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge', 'identifier');

$pageContent = $page->getContent() . '
    <script type="text/javascript">
    //<![CDATA[
        if (navigator.platform.toUpperCase().indexOf("MAC")>=0) {
            $$("body").first().addClassName("ismac");
        }
    //]]>
    </script>
';

$cmsPageData = array(
    'content' => $pageContent
);
$page->addData($cmsPageData)->save();


$installer->endSetup();