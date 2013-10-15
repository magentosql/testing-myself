<?php

$installer = $this;

$installer->startSetup();

if(Mage::helper('wsacommon')->getVersion() == 1.6){
	$installer->run("
	
	UPDATE {$this->getTable('eav_attribute')} SET `is_visible` = '1' WHERE attribute_code='ship_length';  
	UPDATE {$this->getTable('eav_attribute')} SET `is_visible` = '1' WHERE attribute_code='ship_width';  
	UPDATE {$this->getTable('eav_attribute')} SET `is_visible` = '1' WHERE attribute_code='ship_height';  
	UPDATE {$this->getTable('eav_attribute')} SET `is_visible` = '1' WHERE attribute_code='ship_separately';  
	UPDATE {$this->getTable('eav_attribute')} SET `is_visible` = '1' WHERE attribute_code='split_product';  
			
    	
");

} else{
	$installer->run("

		select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_length';
		UPDATE {$this->getTable('catalog_eav_attribute')} SET `is_visible` = '1' WHERE attribute_id=@attribute_id;  
		
		select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_width';
		UPDATE {$this->getTable('catalog_eav_attribute')} SET `is_visible` = '1' WHERE attribute_id=@attribute_id; 
		 
		select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_height';
		UPDATE {$this->getTable('catalog_eav_attribute')} SET `is_visible` = '1' WHERE attribute_id=@attribute_id; 
		 
		select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='ship_separately';
		UPDATE {$this->getTable('catalog_eav_attribute')} SET `is_visible` = '1' WHERE attribute_id=@attribute_id;  
		
		select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='split_product';
		UPDATE {$this->getTable('catalog_eav_attribute')} SET `is_visible` = '1' WHERE attribute_id=@attribute_id;  
		

	");
	
};   

$installer->endSetup();








