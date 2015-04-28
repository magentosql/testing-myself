<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->addFieldToFilter('grand_total', 0)
            ->addFieldToFilter('main_table.created_at', array('gteq' => '2015-04-20'));

        $select = $collection->getSelect();
        $select->joinLeft(
            array('sfoa' => 'sales_flat_order_address'),
            'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"',
            array('sfoa.street', 'sfoa.city', 'sfoa.region', 'sfoa.postcode', 'sfoa.telephone')
        );

        $select->joinLeft(
            'sales_flat_order_item',
            'sales_flat_order_item.order_id = main_table.entity_id',
            array('skus' => new Zend_Db_Expr('group_concat(sales_flat_order_item.sku SEPARATOR "\r\n ")'))
        );
        $select->group('main_table.entity_id');

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridFree', array('_current' => true));
    }

    protected function _prepareColumns()
    {

        $this->addColumn(
            'real_order_id',
            array(
                'header' => Mage::helper('sales')->__('Order #'),
                'width' => '80px',
                'type' => 'text',
                'index' => 'increment_id',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                array(
                    'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                    'index' => 'main_table.store_id',
                    'type' => 'store',
                    'store_view' => true,
                    'display_deleted' => true,
                )
            );
        }

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('sales')->__('Purchased On'),
                'index' => 'created_at',
                'filter_index' => 'main_table.created_at',
                'sort_index' => 'main_table.created_at',
                'type' => 'datetime',
                'width' => '100px',
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('sales')->__('SKU'),
                'width' => '160px',
                'type' => 'text',
                'index' => 'skus',
                'filter_condition_callback' => array('Wsnyc_OrdersReport_Model_Filter', 'filterSkus'),
            )
        );


        $this->addColumn(
            'billing_name',
            array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index' => 'main_table.billing_name',
            )
        );

        $this->addColumn(
            'shipping_name',
            array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index' => 'main_table.shipping_name',
            )
        );

        $this->addColumn(
            'shipping_address',
            array(
                'header' => Mage::helper('sales')->__('Shipping Address'),
                'index' => 'addr',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'wsnyc_ordersreport/adminhtml_sales_order_grid_renderer_address',
                'filter_condition_callback' => array('Wsnyc_OrdersReport_Model_Filter', 'filterAddress'),

            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('sales')->__('Status'),
                'index' => 'status',
                'type' => 'options',
                'width' => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            )
        );

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn(
                'action',
                array(
                    'header' => Mage::helper('sales')->__('Action'),
                    'width' => '50px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url' => array('base' => '*/sales_order/view'),
                            'field' => 'order_id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
                )
            );
        }

        $this->addExportType('*/*/exportFreeCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportFreeExcel', Mage::helper('sales')->__('Excel XML'));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }
}