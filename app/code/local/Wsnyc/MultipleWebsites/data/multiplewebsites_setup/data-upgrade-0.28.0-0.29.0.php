<?php
/**
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

//get product class tax id
$productTaxClasses = Mage::getModel('tax/class')->getCollection()->addFieldToFilter('class_type', array('eq' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT));

//create wholesale customer tax class
$customerTaxClass = Mage::getModel('tax/class')
                        ->setData(array('class_name' => 'Wholesale Customer', 'class_type' => Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER))
                        ->save();

//create wholesale tax rate
$rate = Mage::getModel('tax/calculation_rate');
$rate->setData(array(
                'code' => 'US-WHOLESALE',
                'tax_country_id' => 'US',
                'tax_region_id' => '*',
                'tax_postcode' => '*',
                'rate' => 0.00
        ));
$rate->save();

//create tax rule for wholesale customer tax class and wholesale tax rate
$rule = Mage::getModel('tax/calculation_rule');
$rule->setData(array(
        'code' => 'Wholesale Tax',
        'priority' => 0,
        'position' => 0,
))->save();

foreach($productTaxClasses as $productTaxClass) {
        $calculation = Mage::getModel('tax/calculation');
        $calculation->setData(array(
            'tax_calculation_rate_id' => $rate->getId(),
            'tax_calculation_rule_id' => $rule->getId(),
            'customer_tax_class_id' => $customerTaxClass->getId(),
            'product_tax_class_id' => $productTaxClass->getId()
        ))->save();
}

$groups = Mage::getModel('customer/group')->getCollection()
                ->addFieldToFilter('customer_group_code', array('nin' => array('General', 'NOT LOGGED IN', 'Retailer')));
foreach($groups as $group) {
        $group->setTaxClassId($customerTaxClass->getId())->save();
}
$installer->endSetup();