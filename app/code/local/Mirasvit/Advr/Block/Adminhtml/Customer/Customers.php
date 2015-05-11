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


class Mirasvit_Advr_Block_Adminhtml_Customer_Customers extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setIntervalsVisibility(false)
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $this->getChart()
            ->setXAxisType('category')
            ->setXAxisField('name');

        $this->getGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('desc');

        $this->setHeaderText(Mage::helper('advr')->__('Customers'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $customers = Mage::getResourceModel('advr/customer_collection');
        
        $customers
            ->joinCustomerGroup()
            ->joinOrdersInformation()
            ->joinBillingAddress()
            ->joinPurchasedProducts()
            ;
        // echo $customers->getSelect();die();
        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($customers)
            ->setColumnGroupBy('email')
            ;

        return $collection;
    }

    public function getColumns()
    {
         $columns = array(
            'email' => array(
                'header'                => 'Customer',
                'type'                  => 'text',
                'filter_index'          => 'main_table.email',
                'totals_label'          => 'Total',
                'filter_totals_label'   => 'Subtotal',
                'frame_callback'        => array(Mage::helper('advr/callback'), 'linkToCustomer'),
                'chart'                 => true,
            ),

            'name' => array(
                'header'                => 'Name',
                'type'                  => 'text',
                'filter'                => false,
                'totals_label'          => '',
                'filter_totals_label'   => '',
            ),

            'customer_group_code' => array(
                'header'               => 'Customer Group',
                'type'                 => 'text',
                'index'                => 'customer_group_code',
                'totals_label'         => '',
                'filter_totals_label'  => '',
                'chart'                => false,
            ),

            'created_at' => array(
                'header'               => 'Account Created',
                'type'                 => 'date',
                'index'                => 'main_table.created_at',
                'totals_label'         => '',
                'filter_totals_label'  => '',
                'chart'                => false,
            ),

            'postcode' => array(
                'header'               => 'Postal Code',
                'type'                 => 'text',
                'index'                => 'postcode',
                'totals_label'         => '',
                'filter_totals_label'  => '',
                'chart'                => false,
                'hidden'               => true,
            ),

            'telephone' => array(
                'header'               => 'Telephone',
                'type'                 => 'text',
                'index'                => 'telephone',
                'totals_label'         => '',
                'filter_totals_label'  => '',
                'chart'                => false,
                'hidden'               => true,
            ),

            'company' => array(
                'header'               => 'Company',
                'type'                 => 'text',
                'index'                => 'company',
                'totals_label'         => '',
                'filter_totals_label'  => '',
                'chart'                => false,
                'hidden'               => true,
            ),

            'last_order_at' => array(
                'header'                    => 'Last Order Date',
                'type'                      => 'date',
                'index'                     => 'last_order_at',
                'totals_label'              => '',
                'filter_totals_label'       => '',
                'chart'                     => false,
                'filter_condition_callback' => array($this, 'addFilterByLastOrderDate'),
            ),

            'products' => array(
                'header'                    => 'Purchased Products',
                'index'                     => 'products',
                'totals_label'              => '',
                'filter_totals_label'       => '',
                'chart'                     => false,
                'frame_callback'            => array($this, 'products'),
                'export_callback'           => array($this, 'products'),
                'hidden'                    => true,
            ),
            
            'avg_grand_total' => array(
                'header'         => 'Average Sale',
                'type'           => 'currency',
                'chart'          => true,
            ),

            'sum_grand_total' => array(
                'header'         => 'Lifetime Sales',
                'type'           => 'currency',
                'chart'          => true,
            ),
        );

        return $columns;
    }

    public function addFilterByLastOrderDate($collection, $column)
    {
     
        $condition = $column->getFilter()->getCondition();
        if ($condition) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $having = $connection->prepareSqlCondition(new Zend_Db_Expr('MAX(order_table.created_at)'), $condition);
            $collection->getResourceCollection()->getSelect()
                ->having($having);
        }

        return $this;
    }

    public function products($value, $row, $column)
    {
        $data = array();

        $products = $row->getData('products');
        $rows = explode('@', $products);

        if (count($rows) != 4) {
            return '';
        }
        
        $ids   = explode('^', $rows[0]);
        $names = explode('^', $rows[1]);
        $skus  = explode('^', $rows[2]);
        $qties = explode('^', $rows[3]);

        foreach ($ids as $idx => $id) {
            $name = isset($names[$idx]) ? $names[$idx] : '';
            $sku  = isset($skus[$idx]) ? $skus[$idx] : '';
            $qty  = isset($qties[$idx]) ? $qties[$idx] : '';

            $data[] = '<a class="nobr" href="'.$this->getUrl('adminhtml/catalog_product/edit', array('id' => $id)).'">'
                    .$sku
                    .' / '
                    .Mage::helper('core/string')->truncate($name, 50)
                    .' / '.intval($qty)
                .'</a>';
        }

        return implode('<br>', $data);
    }
}