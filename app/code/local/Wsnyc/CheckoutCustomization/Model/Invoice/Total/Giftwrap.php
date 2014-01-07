<?php

class Wsnyc_CheckoutCustomization_Model_Invoice_Total_Giftwrap extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Wsnyc_CheckoutCustomization_Model_Invoice_Total_Giftwrap
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $invoice->setSubtotal($invoice->getSubtotal()+$order->getOnestepcheckoutGiftwrapAmount());
        $invoice->setBaseSubtotal($invoice->getBaseSubtotal()+$order->getOnestepcheckoutGiftwrapAmount());
        $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax()+$order->getOnestepcheckoutGiftwrapAmount());
        $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax()+$order->getOnestepcheckoutGiftwrapAmount());

        $invoice->setGrandTotal($invoice->getGrandTotal()+$order->getOnestepcheckoutGiftwrapAmount());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$order->getOnestepcheckoutGiftwrapAmount());
        return $this;
    }
}
