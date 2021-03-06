<?php
class Extendware_EWPageCache_Model_Injector_Wishlist_Customer_Sidebar extends Extendware_EWPageCache_Model_Injector_Abstract
{
	public function getInjection(array $params = array(), array $request = array()) {
		$data = null;
		$cacheKey = $this->getCacheKey($params);
		$cache = $this->loadFromCache($cacheKey);
		if ($cache !== false) $data = $cache['data'];
		else {
			$type = isset($params['type']) ? $params['type'] : 'wishlist/customer_sidebar';
			$block = Mage::app()->getLayout()->createBlock($type, $this->getId());
			
			if (empty($params['template']) === true) {
				$params['template'] = 'wishlist/sidebar.phtml';
			}
			$block->setTemplate($params['template']);
			$data = $block->toHtml();
			$this->saveToCache($cacheKey, $data);
		}
		return $data;
	}
}
