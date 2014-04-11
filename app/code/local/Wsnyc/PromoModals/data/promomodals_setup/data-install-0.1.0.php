<?php
$installer = $this;
$pageContent = <<< EOT
<ul>
<li>No minimum purchase required. Offer valid from 5/1/14 at 12:00am PST through 5/31/14 at 11:59pm PST.</li>
<li>Free Shipping cannot be combined with any other offers or discounts.</li>
</ul>
EOT;
$page = Mage::getModel('cms/block')->load('modal-freeshipping-info','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-freeshipping-info')
            ->setTitle('Free Shipping details')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle('Free Shipping details')
        ->save();
	 