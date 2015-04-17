<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$successPage = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-sample-request-success', 'identifier');
$content = $successPage->getContent()."
<!-- Facebook Conversion Code for Checkout -->
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6027244927737', {'value':'0.01','currency':'USD'}]);
</script>
<noscript><img height=\"1\" width=\"1\" alt=\"\" style=\"display:none\" src=\"https://www.facebook.com/tr?ev=6027244927737&cd[value]=0.01&cd[currency]=USD&noscript=1\" /></noscript>
";
$successPageData = array(
    'content' => $content,
    'custom_layout_update_xml' => '<remove name="breadcrumbs" /><reference name="head"><action method="addItem"><type>skin_css</type><file>css/clean-challenge.css</file></action></reference>'
);


$successPage->addData($successPageData )->save();

$installer->endSetup();