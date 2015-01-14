<?php

Mage::getModel('cms/block')->load('block_header_nav_whatsnew','identifier')
        ->setStores(array('1'))
        ->save();
