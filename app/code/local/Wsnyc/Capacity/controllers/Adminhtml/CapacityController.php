<?php

class Wsnyc_Capacity_Adminhtml_CapacityController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Find and resend orders that have not been correctly processed
     */
    public function resendAction()
    {
        $i = 0;
        $errors = 0;
        foreach ($this->_getOrders() as $order) {
            /** @var Mage_Sales_Model_Order $order */

            foreach ($order->getInvoiceCollection() as $invoice) {
                /** @var Mage_Sales_Model_Order_Invoice $invoice */
                if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID) {
                    $this->_sendInvoice($invoice) ? $i++ : $errors++;
                }
            }
        }
        if ($i == 0 && $errors == 0) {
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__('All orders are already sent to capacity'));
        }
        elseif ($errors == 0) {
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__('%s order(s) were succesfully sent to capacity server.', $i));
        }
        elseif ($i > 0) {
            Mage::getSingleton('core/session')->addWarning(Mage::helper('core')->__('%s order(s) were succesfully sent to capacity server but %s order(s) encountered some errors.', $i, $errors));
        }
        else {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('%s order(s) were encountered errors when sending to capacity server.', $errors));
        }
        $this->_redirect('adminhtml/system_config/edit/section/shipping');
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return bool
     */
    protected function _sendInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $event = new Varien_Object();
        $event->setInvoice($invoice);
        $observer = new Varien_Object();
        $observer->setEvent($event);
        return Mage::getSingleton('capacity/observer')->processShipment($observer);
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getOrders()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter('capacity_send_status', array('eq' => 0));

        return $collection;
    }
}