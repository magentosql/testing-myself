<?php
$installer = $this;
$installer->startSetup();

$attributeCode = 'monogram_custom_price';
$entityType = 'catalog_product';

$installer->addAttribute($entityType, $attributeCode, array(
    'type' => 'decimal',
    'label' => 'Monogram Custom Price',
    'source' => NULL,
    'input' => 'price',
    'frontend_class' => 'validate-number',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false
));

foreach($installer->getAllAttributeSetIds($entityType) as $setId) {
    $installer->addAttributeToSet($entityType, $setId, 'Prices', $attributeCode);
}

$installer->endSetup();