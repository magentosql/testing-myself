<?php
$installer = $this;
$pageContent = <<< EOT
<p>Receive free shipping in the US on any order of $50 or more of merchandise (excludes monogramming and gift boxing).</p>
<p>Free shipping cannot be combined with any other offers or discounts.</p>
EOT;
$page = Mage::getModel('cms/block')->load('modal-freeshipping_over_50','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-freeshipping_over_50')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle('Free Shipping Details')
        ->save();
	 