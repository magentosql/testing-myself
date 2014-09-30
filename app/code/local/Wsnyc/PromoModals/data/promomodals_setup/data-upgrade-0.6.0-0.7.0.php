<?php
$installer = $this;
$pageContent = <<< EOT
<ul>
<li>The Laundress will donate $1 for every Wool & Cashmere Shampoo purchased during the month of October to Concourse House in New York. Concourse House’s mission is to cease homelessness by providing families with safe, stable, transitional housing.</li>
<li>The donation period will last from 10/1/14 through 10/31/14.</li>
</ul>
EOT;
$page = Mage::getModel('cms/block')->load('modal-woolinitiative','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-woolinitiative')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle("The Wool & Cashmere Donation Initiative")
        ->save();
	 