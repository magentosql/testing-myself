<?php

$installer = $this;

$pageContent = <<< EOT
<div class="grid4-2 left">
<div class="contact-info">
<h3>The Laundress, Inc</h3>
<address>247&nbsp;W.&nbsp;30th&nbsp;Street, 7F<br />New York, NY 10001</address>
<p>Tel: <span>212-209-0074</span><span class="spacer">|</span>Fax: <span>775-213-7158</span> <a class="email" href="mailto:sales@thelaundress.com">sales@thelaundress.com</a></p>
</div>
<div>
<h3>Wholesale:</h3>
<p>For additional sales support, collateral, or product testers, please contact <a class="email" href="mailto:sales@thelaundress.com">sales@thelaundress.com</a></p>
</div>
<div>
<h3>International Distribution:</h3>
<p><a class="email" href="mailto:distributor@thelaundress.com">distributor@thelaundress.com</a></p>
</div>
<div class="box-note"><span>PLEASE NOTE:</span> If you are unable to place your order online, please <a class="email" href="mailto:sales@thelaundress.com">e-mail</a>, or call us at 212-209-0074. </div>
</div>
<div class="grid4-2 right">
<div class="international-q">
<h3>Training site:</h3>
<p>Please visit our <a href="{{store url='training/'}}">Training Site</a> for washing instructions, product labels, press, and more. 
<br /><br />Username: guest
<br />Login: training</p>
</div>
<div class="international-q">
<h3>FTP Site:</h3>
<p>Please visit our <a href="https://www.dropbox.com/sh/01eyap3w2g7w89u/AABK2TS7SPM-exD2GiBCtVvRa?dl=0">FTP Site</a> for product images, lifestyle images, and branding.</p>
</div>
</div>
EOT;

$pageXmlUpdate = <<< EOT
<reference name="left">
        <block type="core/template" name="contact_sidebar" template="contact/sidebar.phtml" ></block> 
</reference>
EOT;

$retailStore = Mage::getModel('core/store')->load('default','code');
$wholesaleStore = Mage::getModel('core/store')->load('wholesaledefault','code');

$cmsPage = Mage::getModel('cms/page')->getCollection()
        ->addFieldToFilter('identifier', array('eq' => 'customer-service/contact-us'))
        ->addStoreFilter($retailStore)
        ->load()
        ->getFirstItem();
        
$cmsPage->setStores(array($retailStore->getId()))
        ->save();

Mage::getModel('cms/page')
        ->setIdentifier('customer-service/contact-us')
        ->setTitle('Contact Us')
        ->setRootTemplate('two_columns_left')
        ->setIsActive('1')
        ->setStores(array($wholesaleStore->getId()))
        ->setContent($pageContent)
        ->setContentHeading('')
        ->setLayoutUpdateXml($pageXmlUpdate)
        ->save();