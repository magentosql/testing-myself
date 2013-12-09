<?php
$installer = $this;
$installer->startSetup();

$installer->run("
		ALTER TABLE {$this->getTable('sales_flat_order')} 
			ADD `gift_boxed_separately` TINYINT(1) DEFAULT 0;

		ALTER TABLE {$this->getTable('sales_flat_order_item')}
		    ADD `gift_boxed` TINYINT(1) DEFAULT 0;
	");

$installer->run("
        UPDATE {$this->getTable('sales_flat_order_item')} AS `oi`
            JOIN {$this->getTable('sales_flat_order')} AS `o`
                ON oi.order_id=o.entity_id
            SET `oi`.`gift_boxed`=1
            WHERE `o`.`gift_message_id`>0
    ");
$installer->endSetup(); 