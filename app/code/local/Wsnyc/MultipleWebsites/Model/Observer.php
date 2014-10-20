<?php

class Wsnyc_MultipleWebsites_Model_Observer {
    
    public function checkQuantityMultiplier($e) {
        if(Mage::app()->getWebsite()->getCode() != 'wholesale') {
            return;
        }
        $params = Mage::app()->getRequest()->getParams();
        if(Mage::app()->getRequest()->getParam('super_group')) {
            $parentProduct = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('product'));
            $parentQuantityMultiplier = $parentProduct->getQtyMultiplier();
            if($parentQuantityMultiplier == null) {
                $parentQuantityMultiplier = 1;
            }
            $children = Mage::app()->getRequest()->getParam('super_group');
            $errors = array();
            foreach($children as $productId => $desiredQty) {
                $product = Mage::getModel('catalog/product')->load($productId);
                $quantityMultiplier = $product->getQtyMultiplier();
                if($quantityMultiplier == null) {
                    $quantityMultiplier = $parentQuantityMultiplier;
                }
                if($desiredQty%$quantityMultiplier != 0) {
                    $children[$productId] = '0';
                    $errors[$product->getName()] = $quantityMultiplier;
                }
            }
            if(count($errors) > 0) {
                Mage::app()->getRequest()->setParam('quantity_errors', $errors);
            }
            Mage::app()->getRequest()->setParam('super_group', $children);
        } else {
            $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('product'));
            $quantityMultiplier = $product->getQtyMultiplier();
            if($quantityMultiplier == null) {
                $quantityMultiplier = 1;
            }
            $qtyOrdered = Mage::app()->getRequest()->getParam('qty');
            if($qtyOrdered == 0) {
                $qtyOrdered = 1;
            }
            if($qtyOrdered%$quantityMultiplier != 0) {
                Mage::app()->getRequest()->setParam('product', 0);
                Mage::app()->getRequest()->setParam('quantity_errors', array($product->getName() => $quantityMultiplier));
            }
        }
    }
    
    public function checkUpdateQuantityMultiplier($e) {
        if(Mage::app()->getWebsite()->getCode() != 'wholesale') {
            return;
        }
        $cartChanges = Mage::app()->getRequest()->getParam('cart');
        $errors = array();
        foreach($cartChanges as $cartItemId => $dataRequested) {
            try {
                $quoteItem = Mage::getModel('sales/quote_item')->load($cartItemId);
                $product = Mage::getModel('catalog/product')->load($quoteItem->getProductId());
                $quantityMultiplier = $product->getQtyMultiplier();
                if($quantityMultiplier == null) {
                    $parentProduct = $this->_getFirstGroupedParentUrl($product);
                    if($parentProduct != null && $parentProduct->getQtyMultiplier() != null) {
                        $quantityMultiplier = $parentProduct->getQtyMultiplier();
                    } else {
                        $quantityMultiplier = 1;
                    }
                }
                foreach ($dataRequested as $dataType => $dataValue) {
                    if($dataType == 'qty' && $dataValue%$quantityMultiplier != 0) {
                        unset($dataRequested[$dataType]);
                        $errors[$product->getName()] = $quantityMultiplier;
                    }
                }
                $cartChanges[$cartItemId] = $dataRequested;
            } catch (Exception $e) {
                Mage::log("Error while updating quote item's quantity: " . $e->getMessage());
            }
        }
        foreach ($cartChanges as $cartItemId => $dataRequested) {
            if(empty($dataRequested)) {
                unset($cartChanges[$cartItemId]);
            }
        }
        Mage::app()->getRequest()->setParam('cart', $cartChanges);
        if(count($errors) > 0) {
            Mage::app()->getRequest()->setParam('quantity_errors', $errors);
        }
    }
    
    public function displayQuantityMultiplierErrorMessage() {
        if(Mage::app()->getRequest()->getParam('quantity_errors')) {
            foreach(Mage::app()->getRequest()->getParam('quantity_errors') as $name => $qty) {
                Mage::getSingleton('core/session')->addError("The quantity of " . $name . " is incorrect. This product can only be bought in a quantity that's a multiplier of " . $qty);
            }           
            $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
        }
    }
    
    public function processWholesaleWebsiteLoginWorkflow() {
        if(Mage::app()->getWebsite()->getCode() != 'wholesale') {
            return;
        }
        $request = Mage::app()->getRequest();
        $isWholesaleAllowed = false;
        if (Mage::getSingleton('customer/session')->isLoggedIn() && (Mage::getModel('customer/group')->load(Mage::getSingleton('customer/session')->getCustomer()->getGroupId())->getGroupType() == '1' || Mage::getModel('customer/group')->load(Mage::getSingleton('customer/session')->getCustomer()->getGroupId())->getGroupType() == '2')) {
            $isWholesaleAllowed = true;
        }
        if($isWholesaleAllowed == false && $request->getModuleName() != 'customer' && $request->getControllerName() != 'account' && $request->getActionName() != 'login' && $request->getActionName() != 'loginPost') {
            Mage::getSingleton('customer/session')->logout();
            Mage::getSingleton('customer/session')->addError(Mage::helper('multiplewebsites')->__('You need to be logged in as a Wholesaler or a Distributor to be able to browse the wholesale store.'));
            Mage::app()->getResponse()->setRedirect(Mage::getUrl("customer/account/login"));
        }        
        if($request->getModuleName() == 'customer' && $request->getControllerName() == 'account' && $request->getActionName() == 'create') {
            Mage::getSingleton('customer/session')->addError(Mage::helper('multiplewebsites')->__('New accounts for the wholesale store can only be created by administrators.'));
            Mage::app()->getResponse()->setRedirect(Mage::getUrl("customer/account/login"));
        }        
    }
    
    public function saveAdditionalCustomerGroupData($e) {
        if(Mage::getSingleton('adminhtml/session')->getMessages()->getLastAddedMessage()->getType() == 'error') {
            return;
        }
        $request = $e->getEvent()->getControllerAction()->getRequest();
        Mage::getModel('customer/group')->load($request->getParam('code'), 'customer_group_code')
                ->setGroupType($request->getParam('group_type'))
                ->setPriceMultiplier($request->getParam('price_multiplier'))
                ->save();
    }
    
    public function applyCustomerGroupPriceMultiplier($observer) {
        if(Mage::app()->getWebsite()->getCode() != 'wholesale' && Mage::app()->getWebsite()->getId() != 0) {
            return;
        }
        $cartItems = $observer->getEvent()->getQuoteAddress()->getQuote()->getAllVisibleItems();
        $priceMultiplier = Mage::helper('multiplewebsites')->getCustomerGroupPriceModifier();
        foreach ($cartItems as $quoteItem) {
            $basePrice = Mage::getModel('catalog/product')->load($quoteItem->getProductId())->getFinalPrice() * $priceMultiplier;
            $tierPrices = Mage::getModel('catalog/product')->load($quoteItem->getProductId())->getTierPrice();
            if($tierPrices) {
                foreach($tierPrices as $tierPrice) {
                    if(($tierPrice['website_price'] * $priceMultiplier) < $basePrice && $tierPrice['price_qty'] <= $quoteItem->getQty()) {
                        $basePrice = $tierPrice['website_price'] * $priceMultiplier;
                    }
                }
            }
            $newPrice = $basePrice;
            if (is_array($quoteItem->getOptions())) {
                foreach ($quoteItem->getOptions() as $itemOption) {
                    $data = @unserialize($itemOption->getValue());
                    if($data && isset($data['options']) ) {
                        $optionArray = array();
                        foreach($data['options'] as $option => $value) {
                            $optionData = $this->_getOptionValueData($value);
                            if($optionData) {
                                $optionArray[] = $optionData;
                            }

                        }
                        foreach($optionArray as $singleOption) {
                            $newPrice += $this->_processPrice($newPrice, $singleOption['price']*$priceMultiplier, $singleOption['price_type']);
                        }
                    }
                }
            }

            if($basePrice) {
                $quoteItem->setCustomPrice($newPrice);
                $quoteItem->setOriginalCustomPrice($newPrice);
                $quoteItem->getProduct()->setIsSuperMode(true);
                $quoteItem->save();
            }
        }
    }
    
    protected function _getFirstGroupedParentUrl($product) {
        $arrayOfParentIds = Mage::getSingleton("catalog/product_type_grouped")->getParentIdsByChild($product->getId());
        if(isset($arrayOfParentIds[0])) {
            return Mage::getModel('catalog/product')->load($arrayOfParentIds[0]);
        } else {
            return null;
        }
    } 
}

