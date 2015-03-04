<?php

class Wsnyc_CouponParam_Model_Observer {
    
    /**
     * XML Path to system configuration
     */
    const XML_ACTIVE_PATH = 'couponparam/general/active';
    const XML_PARAM_PATH = 'couponparam/general/param_name';
    
    /**
     * Flag for showing empty cart notice
     * 
     * @var boolean
     */
    protected $_showSaveCouponMessage = false;
    
    /**
     * Fetch coupon code param from url and apply to the cart
     * Fired on fronted on controller_action_predispatch event
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function checkCouponParam(Varien_Event_Observer $observer) {
        if (Mage::getStoreConfig(self::XML_ACTIVE_PATH)) {
            $couponCode = $this->_getCouponCode();            
            if (empty($couponCode)) {
                //no coupon in url or in session
                return;
            }
            
            if (!$this->_getQuote()->getItemsCount()) {
                //no items in cart, remember coupon and use it later                
                $this->_saveCouponCode($couponCode);
                return;
            }
            
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= 255;
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '');
            $this->_getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
            $this->_getQuote()->setTotalsCollectedFlag(false);
            Mage::getSingleton('checkout/session')->unsCouponCodeParamValue(); //remove saved coupon code
            if ($isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode()) {
                Mage::getSingleton('core/session')->addSuccess(
                    Mage::helper('wsnyc_couponparam')->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
                );
            }
            else {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('wsnyc_couponparam')->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                );
            }
        }
        
        return;
    }
    
    /**
     * Get customer quote
     * 
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote() {
        return Mage::getSingleton('checkout/cart')->getQuote();
    }
    
    /**
     * Get coupon code from url or session
     *
     * @return string
     */
    protected function _getCouponCode() {
        
        $this->_showSaveCouponMessage = true;
        $couponCode = Mage::app()->getRequest()->getParam(Mage::getStoreConfig(self::XML_PARAM_PATH));        
        if (empty($couponCode)) {
            $couponCode = Mage::getSingleton('checkout/session')->getCouponCodeParamValue(true);
            $this->_showSaveCouponMessage = false;
        }
        
        return $couponCode;
    }
    
    /**
     * Save coupon in session for future use in case cart is empty
     * 
     * @param string $coupon
     */
    protected function _saveCouponCode($coupon) {
        Mage::getSingleton('checkout/session')->setCouponCodeParamValue($coupon);
        
        if ($this->_showSaveCouponMessage) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('wsnyc_couponparam')->__('Your cart is empty at the moment. The coupon code will be applied after your first item is added to cart.')
            );
        }        
    }
}