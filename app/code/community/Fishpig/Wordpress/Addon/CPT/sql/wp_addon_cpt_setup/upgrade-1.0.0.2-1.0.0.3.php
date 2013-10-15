<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */


 	$this->startSetup();
 
 	try {
	 	$this->run("ALTER TABLE {$this->getTable('wp_addon_cpt/type')} ADD COLUMN `display_on_blog` int(1) unsigned NOT NULL default 1;");
	 }
	 catch (Exception $e) {}

	$this->endSetup();
	