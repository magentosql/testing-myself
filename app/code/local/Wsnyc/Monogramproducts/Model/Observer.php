<?php

class Wsnyc_Monogramproducts_Model_Observer
    extends Varien_Event_Observer
{

    public function addMonogramToOrderItem($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();

        $options = $orderItem->getProductOptions();
        if(!is_array($options)) {
            $options = array();
        }

        $monogramOptions = array(
            array('code' => 'monogram-type', 'label' => 'Monogram Type'),
            array('code' => 'monogram-color', 'label' => 'Monogram Color'),
            array('code' => 'monogram-initials', 'label' => 'Monogram Initials'),
        );
        foreach($monogramOptions as $monogram) {
            if(($monogramValue = $this->_getQuoteOption($monogram['code'], $options))) {
                $monogramValue = Mage::helper('core')->htmlEscape($monogramValue);
                $options['options'][] = array(
                    'label' => $monogram['label'],
                    'value' => $monogramValue,
                    'print_value' => $monogramValue,
                    'option_id' => 0,
                    'option_type' => 'text',
                    'option_value' => $monogramValue,
                    'custom_view' => false
                ); 
            }
        }

        $orderItem->setProductOptions($options);
    }

    protected function _getQuoteOption($index, $options)
    {
        if(isset($options['info_buyRequest'][$index])) {
            return $options['info_buyRequest'][$index];
        }
        return '';
    }
}
