<?php

$installer = $this;

$pageContent = <<< EOT
<h3>FAQ</h3>
<p class="subline">Our most commonly asked questions are answered here!</p>
<p>Don&rsquo;t see the answer you need?</p>
<div class="contactus-contact"><a href="mailto:sales@thelaundress.com">Email a Sales Representative</a><span>Call us <br /> <strong>212-209-0074</strong></span></div>
<ul id="accordion-faq" class="accordion">
<li><a href="#question1">Where does The Laundress ship to?</a>
<div>The Laundress ships worldwide.</div>
</li>
<li><a href="#question2">How long will it take my order to arrive?</a>
<div>Most of our products are available immediately—we will notify you if there is a back-order. Please allow 1-2 weeks for shipping. We do not ship on Saturdays, Sundays, or on national holidays.</div>
</li>
<li><a href="#question3">How much does shipping cost?</a>
<div>Unless otherwise specified, all shipments shall be F.O.B. Seller’s warehouse with transportation, freight, handling, and related charges to be added to the total amount charged to Buyer by Seller at the time of shipment. We ship out of New Jersey and the cost of shipping will vary depending on where you are located, the weight of the products being shipped, and how quickly you wish to receive your merchandise. You will be able to preview the cost of shipping prior to placing your order.</div>
</li>
<li><a href="#question4">What happens if a product I want is back-ordered?</a>
<div>Products can become back-ordered during the course of everyday business and we will do our absolute best to let you know what is back-ordered on the website. If there is something you want but cannot find, please contact <a href="mailto:sales@thelaundress.com">sales@thelaundress.com</a>. </div>
</li>
<li><a href="#question5">How can I find out the status of my order?</a>
<div>As soon as your order ships, you will receive a shipping confirmation e-mail to track your order via FedEx or UPS. You can also e-mail us anytime at <a href="mailto:sales@thelaundress.com">sales@thelaundress.com</a>. </div>
</li>
<li><a href="#question6">Can I order by phone?</a>
<div>Yes, please call us Monday - Friday at 212-209-0074 from 10 a.m. - 5 p.m. EST.</div>
</li>
<li><a href="#question7">Can I return merchandise?</a>
<div>The Laundress does not accept any returns of their products. If your shipment has been damaged in transit, please contact us right away and either keep or photo-document the packaging for claims. </div>
</li>
<li><a href="#question8">Can I cancel my order?</a>
<div>You may only cancel your order on the same day it was placed, but not after. </div>
</li>
<li><a href="#question9">Can I make changes to my order?</a>
<div>All order modifications must be submitted within 24 hours of confirmed order receipt from The Laundress. </div>
</li>
<li><a href="#question10">What happens if my order arrives damaged?</a>
<div>If your order is damaged, please e-mail <a href="mailto:sales@thelaundress.com">sales@thelaundress.com</a> within 24 hours of receipt. We'll provide instructions. Please include your order number in your email. Thank you!</div>
</li>
<li><a href="#question11">Will I be charged sales tax?</a>
<div>International orders will infer the cost of all applicable taxes and duties.</div>
</li>
<li><a href="#question12">Can I use your products in a high-efficiency washing machine?</a>
<div>Yes, our products are also formulated for H-E machines. You should use ⅛ cup of detergent versus ¼ cup per load.</div>
</li>
<li><a href="#question13">My dry cleaners cannot remove a stain, what should I do?</a>
<div>We offer "Ask the Laundress" on our website for all your laundry inquiries-just fill out the form online and The Laundress will answer any question you may have about laundry and using The Laundress products.</div>
</li>
<li><a href="#question14">Is The Laundress an environmentally-friendly brand?</a>
<div>Yes, The Laundress cares about the environment--we are a biodegradable and phosphate, allergen & toxic free product.</div>
</li>
<li><a href="#question15">Does The Laundress test on animals?</a>
<div>No, our products are not tested on animals. All products are tested on The Laundress co-founders Gwen & Lindsey.</div>
</li>
</ul>
EOT;

$pageXmlUpdate = <<< EOT
<reference name="left">
        <block type="core/template" name="contact_sidebar" template="contact/sidebar.phtml" ></block> 
</reference>
EOT;

$retailStore = Mage::getModel('core/store')->load('default','code');
$wholesaleStore = Mage::getModel('core/store')->load('wholesaledefault','code');

Mage::getModel('cms/page')->load('faq','identifier')
        ->setStores(array($retailStore->getId()))
        ->save();

Mage::getModel('cms/page')
        ->setIdentifier('faq')
        ->setTitle('FAQ for Orders')
        ->setRootTemplate('two_columns_left')
        ->setIsActive('1')
        ->setStores(array($wholesaleStore->getId()))
        ->setContent($pageContent)
        ->setContentHeading('')
        ->setLayoutUpdateXml($pageXmlUpdate)
        ->save();