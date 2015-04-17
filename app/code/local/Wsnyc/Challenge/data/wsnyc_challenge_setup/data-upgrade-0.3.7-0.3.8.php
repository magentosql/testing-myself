<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$productFormInputs = '';
$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', 'K-15');
if ($_product && $_product->getId()) {
    $productFormInputs .= '<input type="hidden" name="product" value="' . $_product->getId() . '" />';
    $productFormInputs .= '<input type="hidden" name="qty" value="1" />';

    $typeInstance = $_product->getTypeInstance(true);
    $typeInstance->setStoreFilter($_product->getStoreId(), $_product);

    $optionCollection = $typeInstance->getOptionsCollection($_product);

    $selectionCollection = $typeInstance->getSelectionsCollection(
        $typeInstance->getOptionsIds($_product),
        $_product
    );
    $_options = $optionCollection->appendSelections($selectionCollection, false,
        Mage::helper('catalog/product')->getSkipSaleableCheck()
    );

    foreach ($_options as $_option) {
        $_selections = $_option->getSelections();
        $productFormInputs .= '<input type="hidden" name="bundle_option[' . $_option->getId() . ']" value="' . $_selections[0]->getSelectionId() . '"/>';
    }
}

$pageContent = <<< EOT

<div class="main-banner"><img src="{{media url="wysiwyg/30daychallenge/challenge-banner.png"}}" alt="30 Day Clean Home Challenge" width="960" height="422" /></div>
<h1>Take the 30-Day Clean Home Challenge and Say Goodbye to Toxins!</h1>
<hr class="clean-challenge-separator" />
<div class="main-description">
    <p>Want in on a dirty little secret? Most grocery-brand disinfectants designed to clean your space leave a little extra something behind: harsh chemicals and allergens. Kiss toxins goodbye with this arsenal of safe, green, and effective solutions that do your dirty work without the harmful residue and unpleasant smell.</p>
    <p>Have 30 days to dedicate to spring cleaning? That's all you need to detox your home from the kitchen to the laundry room. Simply swap your grocery-brand cleaners with The Laundress products for a fresh and green finish.</p>
</div>
<div class="alternatives">
    <div class="circle bg-black">
        <span class="fancy">Start by getting</span> <span class="big">20% Off</span> <span>any purchase of three or&nbsp;more products by using the code: <strong>30DAYCLEAN</strong></span>
        <a class="button" href="{{store url="for-the-home/home-cleaning}}"><span><span>Shop Now</span></span></a>
    </div>
    <span class="alternative-separator">OR</span>
        <div class="circle bg-white"><span class="fancy">Pick up the</span> <span class="big">Spring Cleaning Bundle</span> <span>- a $120 value for $80</span>
        <a class="button" href="{$_product->getProductUrl()}"><span><span>Shop Now</span></span></a>
    </div>
</div>
<div class="product-view">
    <a href="{$_product->getProductUrl()}"><img src="{{media url="wysiwyg/30daychallenge/products.png"}}" alt="Promotion products" width="883" height="410" /></a>
    <form action="{{store url="/checkout/cart/add"}}" method="post">
        $productFormInputs
        <button class="button btn-cart" title="Add to Bag" type="submit"><span><span>Add to Cart</span></span></button>
    </form>
</div>
<div class="widgets">
    <div class="widget-left"><span class="label">Join us On Facebook &amp; Instagram</span>
        <p>to show off your 30 Day experience and see the results other Laundress customers are having!</p>
        <a href="https://instagram.com/thelaundress/">
            <img src="{{media url="wysiwyg/30daychallenge/social-media.png"}}" alt="Social Media" width="351" height="271" />
        </a>
        <p class="tag">
            <a href="https://instagram.com/thelaundress/">Be sure to tag</a><br />
            <a href="https://instagram.com/thelaundress/"><span>#TL30dayclean @thelaundress</span></a>
        </p>
    </div>
    <div class="widget-right">
        <div class="widget-tips"><span class="label">Top Cleaning Tips</span>
            <ul>
                <li><a href="http://blog.thelaundress.com/wordpress/2014/09/16/how-to-wash-wool-cashmere/">How To Wash Wool and Cashmere</a></li>
                <li><a href="http://blog.thelaundress.com/wordpress/2014/08/18/the-ultimate-how-to-for-cleaning-the-toilet/">The Ultimate 'How To' for Cleaning the Toilet</a></li>
            </ul>
            <hr class="divider" />
            <p class="social-link">Visit us on social media for more tips to help you make the most of the challenge.</p>
            <div class="social-icons">
                <ul>
                    <li><a href="https://instagram.com/thelaundress/" class="instagram" target="_blank">Instagram</a></li>
                    <li><a href="http://pinterest.com/thelaundressny/" class="pintrest" target="_blank">Pintrest</a></li>
                    <li><a href="https://www.facebook.com/thelaundressnyc" class="facebook" target="_blank">Facebook</a></li>
                    <li><a href="https://twitter.com/TheLaundressNY" class="twitter" target="_blank">Twitter</a></li>
                </ul>
            </div>
        </div>
        <div class="widget-newsletter"><span class="label">New To The Laundress?</span>
            <p>Sign up for our newsletter<br />and we'll send you a Free Sample!</p>
            <a href="{{store url="30-day-clean-home-challenge-sample-request"}}" class="button">Get a Free Sample</a></div>
    </div>
</div>
<div class="details"><span class="label">Promotion Details</span>
    <ul>
        <li>Promotional offer period begins April 20, 2015 at 12:01am EST and ends June 10, 2015 at 11:59pm EST</li>
        <li>Coupon code 30DAYCLEAN required for discount redemption</li>
        <li>Discount available on orders containing 3 or more Laundress home cleaning and/or laundry products</li>
        <li>Offer not valid on the Jiffy Steamer</li>
        <li>One sample per user and sample offer valid while supplies last</li>
        <li>This offer cannot be combined with any additional promotions or discounts</li>
    </ul>
    <span class="call">Please call us at (212) 209-0074 with any questions</span>
</div>
<script type="text/javascript">
//<![CDATA[
    if (navigator.platform.toUpperCase().indexOf("MAC")>=0) {
        $$("body").first().addClassName("ismac");
    }
//]]>
</script>
EOT;

$cmsPageData = array(
    'title' => '30 Day Clean Home Challenge',
    'root_template' => 'one_column',
    'identifier' => '30-day-clean-home-challenge',
    'stores' => array(0),//available for all store views
    'content' => $pageContent,
    'custom_layout_update_xml' => '<remove name="breadcrumbs" /><reference name="head"><action method="addItem"><type>skin_css</type><file>css/clean-challenge.css</file></action></reference>'
);

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge', 'identifier');
$page->addData($cmsPageData)->save();

$installer->endSetup();