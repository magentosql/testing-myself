<?php
$installer = $this;
$pageContent = <<< EOT
<ul>
<li>Shop with 25% off of your order using promotional code HOLIDAY. To redeem, simply enter HOLIDAY into the voucher code entry field in your shopping bag and proceed to checkout. This promotion can be redeemed on all Laundress products.</li>
<li>The HOLIDAY promotion is valid from 11/27/2014 12:00 AM EST through 12/1/2014 11:59 PM PST.</li>
</ul>
EOT;
$page = Mage::getModel('cms/block')->load('modal-blackfriday','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-blackfriday')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle("Black Friday & Cyber Monday Details")
        ->save();
	 