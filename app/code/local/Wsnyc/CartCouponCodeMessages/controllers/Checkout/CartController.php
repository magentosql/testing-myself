<?php

/**
 * Shopping cart controller
 */
require_once(BP . DS . 'app' . DS . 'code' . DS . 'core' . DS . 'Mage' . DS . 'Checkout' . DS . 'controllers' . DS . 'CartController.php');

class Wsnyc_CartCouponCodeMessages_Checkout_CartController extends Mage_Checkout_CartController {
    public function indexAction() {
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
                $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                    ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

                $warning = Mage::getStoreConfig('sales/minimum_order/description')
                    ? Mage::getStoreConfig('sales/minimum_order/description')
                    : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

                $cart->getCheckoutSession()->addNotice($warning);
            }
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);
        $allMessages = Mage::getSingleton('checkout/session')->getMessages()->getItems();
        foreach ($allMessages as $singleMessage) {
            if (strpos(strtolower($singleMessage->getText()), 'coupon') !== false || 
                strpos(strtolower($singleMessage->getText()), 'the code has restrictions') !== false) {
                    $couponMessage = $singleMessage->getText();
            }
        }
        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this
            ->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session')
            ->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        if(isset($couponMessage)) {
            $couponBlock = $this->getLayout()->getBlock('checkout.cart.coupon');
            $couponBlock->setCouponMessages($couponMessage);
        }
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');
    }
    
    public function couponPostAction() {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }
        
        if (strlen($couponCode)) {
                
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
            $rule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
            $cond = unserialize($rule->getConditionsSerialized());
            if ($cond && isset($cond['conditions'])) {
                foreach($cond['conditions'] as $condition) {
                    if($condition['attribute'] == 'quote_id') {
                        $quote = Mage::getModel('checkout/session')->getQuote();                        
                        $totals = $quote->getTotals();
                        foreach ($totals as $total) {
                            if($total->getCode() == 'subtotal') {
                                $subtotal = $total->getValue();
                            } elseif ($total->getCode() == 'discount') {
                                $discount = $total->getValue();
                            }
                        }
                        $discountedData = $subtotal + $discount;
                        if($condition['operator'] == '>=') {
                            if($discountedData < $condition['value']) {
                                $this->_getSession()->addError(
                                    $this->__('You have either entered an invalid code or the code has restrictions for certain items in your cart.')
                                );
                                $this->_goBack();
                                return;
                            }
                        }
                        if($condition['operator'] == '>') {
                            if($discountedData <= $condition['value']) {
                                $this->_getSession()->addError(
                                    $this->__('You have either entered an invalid code or the code has restrictions for certain items in your cart.')
                                );
                                $this->_goBack();
                                return;
                            }
                        }
                    }
                }
            }
        }
        

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
                else {
                    $this->_getSession()->addError(
                        $this->__('You have either entered an invalid code or the code has restrictions for certain items in your cart.')
                    );
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

        $this->_goBack();
    }

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart', array('_secure' => true));
        }
        return $this;
    }
}
