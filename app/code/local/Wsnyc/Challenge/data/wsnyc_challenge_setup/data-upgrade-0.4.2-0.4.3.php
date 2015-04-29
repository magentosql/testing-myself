<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$pageContent = <<< EOT
<div class="main-banner"><img src="{{media url="wysiwyg/30daychallenge/request-banner.png"}}" alt="30 Day Clean Home Challenge" width="960" height="313" /></div>
<h1>Thank you for your interest in <br />The Laundress and our Free Sample Program</h1>
<hr class="clean-challenge-separator" />
<div class="main-description">
<p>Due to popular demand, all samples for this promotion have been redeemed. Still interested in receiving a trial packet of our laundry or home cleaning products? <strong>Sign up for our newsletter</strong> to be the first to hear about upcoming Free Sample offers, promotions, and tips!</p>
<p>Don't forget to participate in our <a href="http://www.thelaundress.com/30-day-clean-home-challenge"><strong>30-Day Clean Home Challenge</strong></a>! Enjoy <strong>20% OFF</strong> any purchase of three or more products by using code: <strong>30DAYCLEAN</strong> or pick up the <strong><a href="{{store url="spring-cleaning-bundle"}}">Spring Cleaning Bundle</a></strong><br /> - a $120 value for $80!</p>
</div>
<div class="request-form"><form id="request-form" action="http://thelaundress.us6.list-manage.com/subscribe/post?u=d3d48e75efd637e646b0beb3c&amp;id=dbfb7e7934" method="post" name="mc-embedded-subscribe-form" target="_blank"><fieldset>
<ul class="form-list">
<li class="fields" style="margin-left: auto; margin-right: auto; float: none;">
<div class="field"><label class="required" for="email">Email Address</label>
<div class="input-box"><input id="email" class="input-text required-entry" title="Email Address value=" type="text" name="EMAIL" /></div>
</div>
</li>
</ul>
<div id="billing-buttons-container" class="buttons-set"><button class="button btn-cart" title="Subscribe" type="submit"><span><span>Subscribe Now</span></span></button></div>
</fieldset></form></div>
<div class="details"><span class="label">Promotion Details</span>
<ul>
<li>Promotional offer period begins April 20, 2015 at 12:01am EST and ends June 10, 2015 at 11:59pm EST</li>
<li>One sample per user</li>
<li>Valid while supplies last</li>
<li>The Spring Cleaning Bundle is available while supplies last</li>
</ul>
<span class="call">Please call us at (212) 209-0074 with any questions</span></div>
<script type="text/javascript">
//<![CDATA[
    var form = new VarienForm('request-form');
    $('request-form').observe('submit', function() {
        if(form.validator && form.validator.validate()){
            if (typeof ga == "function") {
                ga('send', 'event', 'newsletter', 'signup', 'Sample Request Newsletter Signup');
            }
            form.submit();
        }
        return false;
    });
//]]>
</script>
EOT;

$cmsPageData = array(
    'content' => $pageContent,
);

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-sample-request', 'identifier');
$page->addData($cmsPageData)->save();

$installer->endSetup();