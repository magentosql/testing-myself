<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     370
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Adminhtml_ProductController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('advr');

        $this->_title($this->__('Advanced Reports'))
            ->_title($this->__('Products'));

        return parent::_initAction();
    }

    public function bestsellerAction()
    {
        $this->_initAction();

        $this->_title($this->__('Bestsellers'));

        $this->renderLayout();
    }

    public function productsAction()
    {
        $this->_initAction();

        $this->_title($this->__('All Products'));

        $this->renderLayout();
    }

    public function attributeAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales By Attribute'));

        $this->renderLayout();
    }

    public function lowstockAction()
    {
        $this->_initAction();

        $this->_title($this->__('Low stock'));

        $this->renderLayout();
    }

    public function productdetailAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')->load($id);
            Mage::register('current_product', $product);
        }

        $this->_initAction();

        $this->_title($this->__('Product Detail'));

        $this->renderLayout();
    }

    public function attributedetailAction()
    {
        if ($attrCode = $this->getRequest()->getParam('attribute')) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attrCode);
            Mage::register('current_attribute', $attribute);

            $value = $this->getRequest()->getParam('value');
            Mage::register('current_value', $value);

            $option = null;
            $options = $attribute->getSource()->getAllOptions(false);
            foreach ($options as $opt) {
                if ($opt['value'] == $value) {
                    $option = $opt['label'];
                }
            }

            Mage::register('current_option', $option);
        }

        $this->_initAction();

        $this->_title($this->__('Attribute Detail'));

        $this->renderLayout();
    }
}