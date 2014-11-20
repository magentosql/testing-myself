<?php

class Wsnyc_Seo_Block_Head extends Diglin_UIOptimization_Block_Optimize_Head {

    /**
     * Set robots depending on store part
     * 
     * @return string
     */
    public function getRobots() {
        if (empty($this->_data['robots'])) {
            $robots = Mage::getStoreConfig('design/head/default_robots');
            $module = Mage::app()->getRequest()->getModuleName();            
            if ($module == 'catalogsearch') {
                $robots = 'NOINDEX,FOLLOW';
            }
            elseif ($module == 'catalog') {
                if (Mage::registry('current_category')) {
                    $filtered = count(Mage::getSingleton('catalog/layer')->getState()->getFilters());
                    $robots = $filtered > 0 ? 'NOINDEX,FOLLOW' : 'INDEX,FOLLOW';
                }
            }
            elseif ($module == 'customer') {
                $robots = 'NOINDEX,NOFOLLOW';
            }
            elseif ($module == 'checkout') {
                $robots = 'NOINDEX,NOFOLLOW';
            }
            
            $this->_data['robots'] = $robots;
        }
        return $this->_data['robots'];
    }

    public function getCanonicalUrl() {
        Mage::dispatchEvent('uioptimization_canonicalurl_before', array('head' => $this, 'transport' => ''));

        if (is_string($this->transport) && strlen($this->transport) > 0) {
            return $this->transport;
        }

        $handles = Mage::app()->getLayout()->getUpdate()->getHandles();

        $searchHandles = array('catalogsearch_result_index', 'catalogsearch_advanced_index', 'catalogsearch_advanced_result');
        $productHandles = array('catalog_product_view');
        $homeHandles = array('cms_index_index');
        $blogHandles = array('blog_post_view', 'blog_cat_view');

        foreach ($handles as $handle) {
            // Catalog Search 
            if (in_array($handle, $searchHandles) && Mage::getStoreConfigFlag('uioptimization/seo/search')) {
                return $this->_setCanonicalSearchUrl();
            } elseif (!Mage::getStoreConfigFlag('uioptimization/seo/search')) {
                return;
            }

            // Catalog Product
            if (in_array($handle, $productHandles) && Mage::getStoreConfigFlag('uioptimization/seo/products')) {
                return $this->_setCanonicalProductUrl();
            } elseif (!Mage::getStoreConfigFlag('uioptimization/seo/products')) {
                return;
            }

            // Homepage CMS
            if (in_array($handle, $homeHandles) && Mage::getStoreConfigFlag('uioptimization/seo/cmshome')) {
                return $this->_setCanonicalHomeUrl();
            } elseif (!Mage::getStoreConfigFlag('uioptimization/seo/cmshome')) {
                return;
            }
        }
        $module = Mage::app()->getRequest()->getModuleName();

        if ($module == 'customer') {
            return;
        } elseif ($module == 'catalog') {
            return $this->_setCanonicalCategoryUrl();
        }
        return $this->_setCanonicalUrl();
    }

    private function _setCanonicalUrl() {
        if (empty($this->_data['urlKey'])) {
            $url = Mage::helper('core/url')->getCurrentUrl();
            $parsedUrl = parse_url($url);
            $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;
            $headUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ($port && '80' != $port ? ':' . $port : '') . $parsedUrl['path'];
            $this->_data['urlKey'] = Mage::helper('uioptimization')->getTrailingSlash($headUrl);
        }
        return $this->_data['urlKey'];
    }

    /**
     * Canonical URL for homepage
     */
    private function _setCanonicalHomeUrl() {
        if (empty($this->_data['urlKey'])) {
            $url = Mage::helper('core/url')->getCurrentUrl();
            $request = Mage::app()->getRequest();
            $currentUri = $request->getRequestUri();

            // Purpose: provide a canonical url for shop having store code in header for the homepage
            if ($request->getBaseUrl() && strpos($currentUri, $request->getBaseUrl()) !== false) {
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . substr($currentUri, strlen($request->getBaseUrl()) + 1);

                $currentUriClean = trim($currentUri, '/');
                $parts = explode('/', $currentUriClean);

                // Add code store to url if not provided by the client
                if (count($parts) <= 1 && Mage::getStoreConfigFlag('web/url/use_store') && Mage::getStoreConfigFlag('web/seo/use_rewrites')) {
                    $url .= Mage::app()->getStore()->getCode() . '/';
                }
            }

            $parsedUrl = parse_url($url);
            $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;
            $headUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ($port && '80' != $port ? ':' . $port : '') . $parsedUrl['path'];
            $this->_data['urlKey'] = Mage::helper('uioptimization')->getTrailingSlash($headUrl);
        }
        return $this->_data['urlKey'];
    }

    /**
     * Canonical Product URL
     */
    private function _setCanonicalProductUrl() {
        if (empty($this->_data['urlKey'])) {
            $product_id = $this->getRequest()->getParam('id');
            $_item = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($product_id);
            $headUrl = Mage::getBaseUrl() . $_item->getUrlPath() /* . Mage::helper('catalog/product')->getProductUrlSuffix() */;
            $this->_data['urlKey'] = Mage::helper('uioptimization')->getTrailingSlash($headUrl);
        }
        return $this->_data['urlKey'];
    }

    private function _setCanonicalSearchUrl() {
        if (empty($this->_data['urlKey'])) {

            $headUrl = $this->_setCanonicalUrl();
            $request = Mage::app()->getRequest();
            $searchParam = $request->getParam('q');
            $searchNameParam = $request->getParam('name');
            $searchDescParam = $request->getParam('description');
            $params = array();
            if ($searchParam)
                $params[] = 'q=' . $searchParam;
            if ($searchNameParam)
                $params[] = 'name=' . $searchNameParam;
            if ($searchDescParam)
                $params[] = 'description=' . $searchDescParam;
            $this->_data['urlKey'] = $headUrl . ((count($params)) ? '?' . implode('&', $params) : '');
        }
        return $this->_data['urlKey'];
    }

    protected function _setCanonicalCategoryUrl() {
        if (empty($this->_data['urlKey'])) {
            $currentCategory = Mage::registry('current_category');
            $this->_data['urlKey'] = $currentCategory->getUrl();
        }
        return $this->_data['urlKey'];
    }

}
