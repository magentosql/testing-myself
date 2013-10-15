<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

 	$this->startSetup();
 
 	$this->run("
 		
		CREATE TABLE IF NOT EXISTS {$this->getTable('wp_addon_cpt/type')} (
			`type_id` int(11) unsigned NOT NULL auto_increment,
			`post_type` varchar(255) NOT NULL default '',
			`slug` varchar(128) NOT NULL default '',
			`name` varchar(255) NOT NULL default '',
			`singular_name` varchar(255) NOT NULL default '',
			`has_archive` int(1) unsigned NOT NULL default 1,
			`post_list_template` varchar(255) NOT NULL default '',
			`post_view_template` varchar(255) NOT NULL default '',
			PRIMARY KEY (`type_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='WordPress Integration: CustomPostTypes - Type';
				
		CREATE TABLE IF NOT EXISTS {$this->getTable('wp_addon_cpt/type_store')} (
			`type_id` int(11) unsigned NOT NULL auto_increment,
			`store_id` smallint(5) unsigned NOT NULL default 0,
			PRIMARY KEY(`type_id`, `store_id`),
			KEY `FK_TYPE_ID_WP_ADDON_CPT_TYPE_STORE` (`type_id`),
			CONSTRAINT `FK_TYPE_ID_WP_ADDON_CPT_TYPE_STORE` FOREIGN KEY (`type_id`) REFERENCES `{$this->getTable('wp_addon_cpt/type')}` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_WP_ADDON_CPT_TYPE_STORE` (`store_id`),
			CONSTRAINT `FK_STORE_ID_WP_ADDON_CPT_TYPE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='WordPress Integration: CustomPostTypes - Store';
	
	");

	$this->endSetup();
	