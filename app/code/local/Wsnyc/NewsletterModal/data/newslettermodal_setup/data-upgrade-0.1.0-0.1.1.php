<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$newsletterBlock = Mage::getModel('cms/block')->load('newsletter-info', 'identifier');
$newContent = <<< EOT
<p><img src="{{media url="wysiwyg/newsletter-content.png"}}" alt="Newsletter" /></p>
EOT;

$newsletterBlock->setContent($newContent)->save();