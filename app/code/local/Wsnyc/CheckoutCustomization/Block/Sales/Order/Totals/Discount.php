<?php
class Wsnyc_CheckoutCustomization_Block_Sales_Order_Totals_Discount extends Mage_Sales_Block_Order_Totals
{
	public function initTotals()
    {
		if($this->discountAmount() != 0){
			$total = new Varien_Object();
			$total->setCode('onestepcheckoutdiscount');
			$total->setValue(-$this->discountAmount());
			$total->setBaseValue(0);
			$total->setLabel('Discount(Admin)');
			$parent = $this->getParentBlock();
			$parent->addTotal($total,'subtotal');
		}
	}
	
	public function discountAmount() {
		$order = $this->getParentBlock()->getOrder();
		// $order->setData('onestepcheckout_discount_amount',123)->save();
		// var_dump($order->getData());
		$discountAmount = $order->getOnestepcheckoutDiscountAmount();
		return $discountAmount;
	}
}
