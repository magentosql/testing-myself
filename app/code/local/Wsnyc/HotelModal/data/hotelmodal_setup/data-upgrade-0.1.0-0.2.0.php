<?php
$installer = $this;
$pageContent = <<< EOT
<ul>
<li>Free Hotel Laundry Bag with purchase details. A FREE Hotel Laundry Bag will be added to your purchase. While the item won’t appear in your shopping cart, confirmation of the Free Gift will appear in your order confirmation email. </li>
<li>Free Gift available while supplies last. Limit one Free Gift per customer. No minimum purchase required to qualify. Offer valid from 3/26/14 at 12:00am PST through 3/28/14 at 11:59pm PST.</li>
</ul>
EOT;
$page = Mage::getModel('cms/block')->load('hotel-info','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('hotel-info')
            ->setTitle('Free Gift with purchase details')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle('Free Gift with purchase details')
        ->save();
	 