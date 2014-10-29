<?php
$installer = $this;
$pageContent = <<< EOT
<ul>
<li>Our gift to you - enjoy 15% off of your order using promotional code NOVEMBERGIFT. To redeem, simply enter NOVEMBERGIFT into the voucher code entry field in your shopping bag and proceed to checkout.</li>
<li>This promotion can be redeemed on all Laundress products. This offer is not valid on Beckel Canvas, Brabantia, or Fog Linen items or on the Jiffy Steamer. Offer cannot be applied to previous purchases.</li>
<li>NOVEMBERGIFT is valid from 11/3/2014 11:59 AM EST through 11/10/2014 11:59 PM EST.</li>
</ul>
EOT;
$page = Mage::getModel('cms/block')->load('modal-novembergift','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-novembergift')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle("November Gift")
        ->save();
	 