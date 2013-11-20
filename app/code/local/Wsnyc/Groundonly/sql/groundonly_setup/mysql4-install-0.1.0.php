<?php
$installer = $this;
$installer->startSetup();

$attributeCode = 'ground_only';
$entityType = 'catalog_product';

$installer->addAttribute($entityType, $attributeCode, array(
    'type' => 'int',
    'label' => 'Ground Only',
    'source' => 'eav/entity_attribute_source_boolean',
    'input' => 'select',
    'frontend_class' => '',
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
    $installer->addAttributeToSet($entityType, $setId, 'Shipping', $attributeCode);
}

$installer->endSetup();