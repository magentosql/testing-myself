<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$pageContent = <<< EOT
<div class="main-banner"><img src="{{media url="wysiwyg/30daychallenge/challenge-banner.png"}}" alt="30 Day Clean Home Challenge" width="960" height="422" /></div>
<h1>Thank You for taking the 30-Day Clean Home Challenge!</h1>
<hr class="clean-challenge-separator" />
<div class="main-description">
<p>Congratulations! You've officially kissed toxins goodbye with your arsenal of safe, green, and effective laundry and home cleaning solutions and completed the 30-Day Clean Home Challenge.</p>
<p>Keep your home tidy and toxin-free year round by signing up for The Laundress Newsletter or follow us on Facebook and Instagram for your daily dose of helpful tips and how-tos</p>
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

<div class="widgets">
<div class="widget-left"><span class="label">Join us On Facebook &amp; Instagram</span>
<p>to show off your 30 Day experience and see the results other Laundress customers are having!</p>
<a href="https://instagram.com/thelaundress/"> <img src="{{media url="wysiwyg/30daychallenge/social-media.png"}}" alt="Social Media" width="351" height="271" /> </a>
<p class="tag"><a href="https://instagram.com/thelaundress/">Be sure to tag</a><br /> <a href="https://instagram.com/thelaundress/"><span>#TL30dayclean @thelaundress</span></a></p>
</div>
<div class="widget-right">
<div class="widget-tips"><span class="label">Top Cleaning Tips</span>
<ul>
<li><a href="http://blog.thelaundress.com/wordpress/2015/04/20/30-day-clean-home-challenge-day-1-tip/">Tip 1: Skip the dry cleaners, wash with Wool &amp; Cashmere Shampoo</a></li>
<li><a href="http://blog.thelaundress.com/wordpress/2015/04/21/30-day-clean-home-challenge-day-2-tip/">Tip 2: Add 2 capfuls of All-Purpose Bleach Alternative to your washing machine for a deep clean</a></li>
</ul>
<hr class="divider" />
<p class="social-link"><a href="{{media url="wysiwyg/30daychallenge/TheLaundress-30DayCleanHomeChallenge-Printable.pdf"}}" target="_blank">Download your 30-Day Clean Checklist here to make sure you didn't miss a tip!</a></p>
<div class="social-icons">
<ul>
<li><a class="instagram" href="https://instagram.com/thelaundress/" target="_blank">Instagram</a></li>
<li><a class="pintrest" href="http://pinterest.com/thelaundressny/" target="_blank">Pintrest</a></li>
<li><a class="facebook" href="https://www.facebook.com/thelaundressnyc" target="_blank">Facebook</a></li>
<li><a class="twitter" href="https://twitter.com/TheLaundressNY" target="_blank">Twitter</a></li>
</ul>
</div>
</div>
<div class="widget-newsletter"><span class="label">New To The Laundress?</span>
<p>Sign up for our newsletter<br />and we'll send you a Free Sample!</p>
<a class="button" href="{{store url="30-day-clean-home-challenge-sample-request"}}">Get a Free Sample</a></div>
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
<span class="call">Please call us at (212) 209-0074 with any questions</span></div>
<script type="text/javascript">// <![CDATA[
    if (navigator.platform.toUpperCase().indexOf("MAC")>=0) {
        $$("body").first().addClassName("ismac");
    }
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
// ]]></script>
EOT;

$cmsPageData = array(
    'title' => '30 Day Clean Home Challenge',
    'root_template' => 'one_column',
    'identifier' => '30-day-clean-home-challenge-end',
    'stores' => array(0),//available for all store views
    'content' => $pageContent,
    'custom_layout_update_xml' => '<remove name="breadcrumbs" /><reference name="head"><action method="addItem"><type>skin_css</type><file>css/clean-challenge.css</file></action></reference>'
);

$page = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-end', 'identifier');
$page->addData($cmsPageData)->save();

$installer->endSetup();