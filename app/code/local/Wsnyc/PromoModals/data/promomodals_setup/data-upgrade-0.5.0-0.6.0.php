<?php
$installer = $this;
$pageContent = <<< EOT
<p>Celebrate Lindsey's birthday and receive 25% off of your order using coupon code LINDSEY14. To redeem, simply enter LINDSEY14 in the voucher code entry field in your shopping bag and proceed to checkout.</p>
<p>This promotion can be redeemed from 07/18/2014 at 11:59 PM EST until 08/01/2014 11:59 PM EST. This is not valid on prior purchases.</p>
EOT;
$page = Mage::getModel('cms/block')->load('modal-lindsey14','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-lindsey14')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle("Lindsey's Birthday Promotion")
        ->save();
	 