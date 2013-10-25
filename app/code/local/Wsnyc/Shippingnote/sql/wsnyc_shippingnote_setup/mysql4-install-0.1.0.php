<?php
$installer = $this;

$installer->startSetup();

$attributeData = array(
    'type' => 'text',
    'label' => 'Shipping note',
    'source' => NULL,
    'input' => 'textarea',
    'class' => '',
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
);

$installer->addAttribute('catalog_product', 'shipping_note', $attributeData);
$idAttribute = $installer->getAttribute('catalog_product', 'shipping_note', 'attribute_id');

$attSet = Mage::getModel('eav/entity_type')->getCollection()->addFieldToFilter('entity_type_code','catalog_product')->getFirstItem();
$attSetCollection = Mage::getModel('eav/entity_type')->load($attSet->getId())->getAttributeSetCollection();

foreach ($attSetCollection as $a) {
    $set = Mage::getModel('eav/entity_attribute_set')->load($a->getId());
    $setId = $set->getId();
    $group = Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_set_id',$setId)->addFieldToFilter('attribute_group_name', 'General')->setOrder('attribute_group_id',ASC)->getFirstItem();
    $groupId = $group->getId();
    $newItem = Mage::getModel('eav/entity_attribute');
    $newItem->setEntityTypeId($attSet->getId())
        ->setAttributeSetId($setId)
        ->setAttributeGroupId($groupId)
        ->setAttributeId($idAttribute)
        ->setSortOrder(300)->save();
    }

$installer->endSetup();