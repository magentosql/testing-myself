<?php

class Wsnyc_OrdersReport_Model_Filter
{
    public function filterSkus($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->getSelect()->having(
            "group_concat(`sales_flat_order_item`.sku SEPARATOR ', ') like ?",
            "%$value%"
        );

        return $this;
    }
}