<?php
$installer = $this;
$content = <<< EOT
<h4 class="marketing-title"><a href="http://www.thelaundress.com/laundry-fabric-care/closet-and-storage">Closet &amp; Storage</a></h4>
<p><a href="http://www.thelaundress.com/laundry-fabric-care/closet-and-storage">Preserve and organize your wardrobes and linens</a></p>
EOT;

Mage::getModel('cms/block')->load('block_header_nav_featured','identifier')
        ->setStores(array('1'))
        ->save();

Mage::getModel('cms/block')
        ->setIdentifier('block_header_nav_featured')
        ->setTitle('Home Featured')
        ->setIsActive('1')
        ->setStores(array('2'))
        ->setContent($content)
        ->save();
	 