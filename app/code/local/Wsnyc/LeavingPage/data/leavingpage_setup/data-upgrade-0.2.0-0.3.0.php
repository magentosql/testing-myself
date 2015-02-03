<?php

$installer = $this;

$blockContent = <<< EOT
<h3>We're sorry to see you go!</h3>
<p>If you decide to stay with us a little longer, we'd like to offer you a 10% promotion - just use this discount code when checking out:</p>
EOT;

Mage::getModel('cms/block')->load('pageleave-modal','identifier')
        ->setContent($blockContent)
        ->save();