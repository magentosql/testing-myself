<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('wsnyc_homepagebanner')}
    ADD `banner_content_position` INT(1) NOT NULL DEFAULT 2 AFTER `banner_content`
    ");

$installer->endSetup();