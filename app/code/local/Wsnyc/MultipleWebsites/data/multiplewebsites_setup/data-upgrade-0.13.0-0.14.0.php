<?php
$installer = $this;
$pageContent = <<< EOT
<h2><span style="font-size: x-large;">Customer Service&nbsp;</span></h2>
<h2><span style="text-decoration: underline;">Wholesale Orders</span></h2>
<p><span style="font-size: small;">Ask about our products, an order you placed, and more.</span></p>
<div class="contactus-contact"><a href="mailto:sales@thelaundress.com">Email our Sales Team</a></div>
<p class="left-content"><span style="font-size: small; font-family: 'book antiqua', palatino; color: #333333;">Or call us, we're happy to help!&nbsp;</span><br /><span style="font-size: small; font-family: georgia, palatino; color: #999999;"><span style="color: #333333; font-family: 'book antiqua', palatino;">212-209-0074&nbsp;Monday- Friday 9am to 5pm EST</span>&nbsp;</span></p>
<p><img style="position: absolute; top: 0; right: -50px; z-index: 0;" src="{{media url='wysiwyg/contactus/contactus.jpg'}}" alt="" /></p>
EOT;

$page = Mage::getModel('cms/page')->load('customer-service','identifier')
        ->setStores(array('1'))
        ->save();

$page = Mage::getModel('cms/page')
        ->setIdentifier('customer-service')
        ->setTitle('Customer Service')
        ->setRootTemplate('two_columns_left')
        ->setIsActive('1')
        ->setStores(array('2'))
        ->setContent($pageContent)
        ->setContentHeading('Customer Service')
        ->save();
	 