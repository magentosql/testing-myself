<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass())
      //      ->addFieldToFilter('grand_total', 0)
            ->addFieldToFilter('main_table.created_at', array('gteq' => '2013-04-20'));

        $select = $collection->getSelect();
        $select->joinLeft(
            array('sfoa' => 'sales_flat_order_address'),
            'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"',
            array('sfoa.lastname','sfoa.firstname','sfoa.street', 'sfoa.city', 'sfoa.region_id', 'sfoa.postcode')
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
                'filter' => false,
                'sortable' => false,
                'filter_condition_callback' => array('Wsnyc_OrdersReport_Model_Filter', 'filterSkus'),
            )
        );

        $this->addColumn(
            'billing_name',
            array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index' => 'billing_name',
            )
        );

        $this->addColumn(
            'shipping_firstname',
            array(
                'header' => Mage::helper('sales')->__('Ship to First Name'),
                'index' => 'firstname',
                'filter_index' => 'sfoa.firstname',
            )
        );
        $this->addColumn(
            'shipping_lastname',
            array(
                'header' => Mage::helper('sales')->__('Ship to Last Name'),
                'index' => 'lastname',
                'filter_index' => 'sfoa.lastname',
            )
        );

        $this->addColumn(
            'shipping_address1',
            array(
                'header' => Mage::helper('sales')->__('Address 1'),
                'filter' => false,
                'sortable' => false,
                'renderer' => 'wsnyc_ordersreport/adminhtml_sales_order_grid_renderer_address1',

            )
        );
        $this->addColumn(
            'shipping_address2',
            array(
                'header' => Mage::helper('sales')->__('Address 2'),
                'filter' => false,
                'sortable' => false,
                'renderer' => 'wsnyc_ordersreport/adminhtml_sales_order_grid_renderer_address2',
            )
        );

        $this->addColumn(
            'shipping_city',
            array(
                'header' => Mage::helper('sales')->__('City'),
                'index' => 'city',
                'filter_index' => 'sfoa.city',
            )
        );

        $regions = array();
        foreach (Mage::getModel('directory/region_api')->items('US') as $region) {
            $regions[$region['region_id']] = $region['code'];
        }

        $this->addColumn(
            'shipping_state',
            array(
                'header' => Mage::helper('sales')->__('State'),
                'index' => 'region_id',
                'type' => 'options',
                'filter_index' => 'sfoa.region_id',
                'options' => $regions,
            )
        );

        $this->addColumn(
            'shipping_zip',
            array(
                'header' => Mage::helper('sales')->__('ZIP'),
                'index' => 'postcode',
                'filter_index' => 'sfoa.postcode',
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