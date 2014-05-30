<?php
$installer = $this;
$pageContent = <<< EOT
<p>Visit <a href="https://offerpop.com/ad.psp?cid=621381&ref=WebsiteHeader" target="_blank">Facebook</a> to enter between June 2, 2014 at 12:00am EST and June 30, 2014 at 11:59pm EST.</p>
<p>Voting period begins July 1, 2014 at 12:00am EST and ends July 31, 2014 at 11:59pm EST.</p>
<p>Two lucky winners will be selected from the top 20 entries based on fan votes. Each winner will receive $500 of FREE pre-selected Laundress products.</p>
EOT;
$page = Mage::getModel('cms/block')->load('modal-photocontest_june_2014','identifier');
if($page->isObjectNew()) {
    $page->setIdentifier('modal-photocontest_june_2014')
            ->setTitle('The Laundress Wash & Win Photo Contest')
            ->setIsActive('1')
            ->setStores(array('0'));
} 
$page->setContent($pageContent)
        ->setTitle('The Laundress Wash & Win Photo Contest')
        ->save();
	 