<?php

/*
** SearchSpring Data Feed Generator
**
** Copyright (c) 2011 B7 Interactive, LLC. All rights reserved.
** Portions Copyright (c) 2011 Method Merchant, Inc.
**
** This file and its contents may not be copied, modified or
** re-distributed with the prior written authorization by an
** officer of B7 Interactive, LLC.
**
** Use of this software is governed by the SearchSpring
** Terms of Service, available here:
** https://manage.searchspring.net/signup/terms
**
*/

session_start();
set_time_limit(0);
//ignore_user_abort();
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', true);

$paging_start = 0;
$paging_count = 100;

if (PHP_SAPI == 'cli') {
	// e.g., $ php searchspring.php store_name
	$store = isset($argv[1]) ? $argv[1] : 'default';
	$paging_start = isset($argv[2]) ? intval($argv[2]) : 0;
	$paging_count = isset($argv[3]) ? intval($argv[3]) : 100;

} else {
	header("Content-Type: text/plain;");
	$show_stores = isset($_GET['show_stores']) ? $_GET['show_stores'] : 'off';
	$store = isset($_GET['store']) ? $_GET['store'] : 'default';
	$paging_start = isset($_GET['start']) ? intval($_GET['start']) : 0;
	$paging_count = isset($_GET['count']) ? intval($_GET['count']) : 100;
}


$core_fields = array(
	'sku',
	'product_type',
	'name',
	'manufacturer',
	'category',
	'url',
	'image_url',
	'thumbnail_url',
	'quantity',
	'in_stock',
	'sale_price',
	'price',
	'description',
	'short_description'
);

$outputFile = 'searchspring_'.$store.'.xml.tmp';

//Making sure that the configuration file is available
if(!file_exists("../app/Mage.php")) {
	exit('Could not find Mage.php.  Make sure the module files are placed in the same directory as the app folder.');
}
else {
	require_once '../app/Mage.php';
}

// let's make sure we can write file
if(!is_writable(".")) {
	exit("Can not write to current directory");
}

//Returning a list of available stores
if ($show_stores == 'on') {
	$stores = Mage::app()->getStores();
	foreach ($stores as $i) { print $i->getCode() . "\n";}
	exit;
}

Mage::app($store);
$storeId = Mage::app()->getStore()->getId();

$includeOutOfStock = FALSE;
$includeZeroPrice = FALSE;

try {
	$xml = '';

	if ($paging_start == 0) {
		$fh = fopen($outputFile, 'w');
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<Products>\n";
		fwrite($fh, $xml);
		fclose($fh);
	}

	$fh = fopen($outputFile, 'a');

// Code to pull in Magento CMS Pages
//	if($paging_start == 0) {
//
//		$pages = Mage::getModel('cms/page')->getCollection();
//
//		foreach($pages as $page) {
//			$doc = new DOMDocument("1.0");
//			$node = $doc->createElement("Product");
//			$productElement = $doc->appendChild($node);
//
//			$page_id = 'page-' . $page->getId();
//			$page_sku = $page->getIdentifier();
//			$page_author = '';
//			$page_title = $page->getTitle();
//			$page_url = Mage::helper('cms/page')->getPageUrl($page->getId());
//			$page_short = '';
//			$page_full = $page->getContent();
//			$page_date = date('F Y', strtotime($page->getCreationTime()));
//			$page_raw_date = $page->getCreationTime();
//
//			addChildToDomElement($productElement, 'ss_type', 'page');
//			addChildToDomElement($productElement, 'entity_id', $page_id);
//			addChildToDomElement($productElement, 'sku', $page_sku);
//			addChildToDomElement($productElement, 'name', $page_title);
//			addChildToDomElement($productElement, 'url', $page_url);
//			addChildToDomElement($productElement, 'description', preg_replace('/[^(\x20-\x7F)\x0A]*/','', $page_full));
//			addChildToDomElement($productElement, 'creation_date', $page_date);
//			addChildToDomElement($productElement, 'raw_date', $page_raw_date);
//
//			$xml = $productElement->ownerDocument->saveXML($productElement);
//			fwrite($fh, $xml);
//
//		}
//	}


	//Assigning the type of products to return (simple, configurable, grouped)
	$typIds = array();
	$typIds[0] = 'simple';
	$typIds[1] = 'configurable';
	$typIds[2] = 'grouped';
	$typIds[3] = 'bundle';

	//Retrieving the product ids
	$products = Mage::getModel('catalog/product')->getCollection();
	$products = $products->addStoreFilter($storeId);
	$products->addAttributeToFilter('status', 1);
	$products->addAttributeToFilter('type_id', array('in' => $typIds));
	$products->addAttributeToFilter('visibility', 4);
	$products->addAttributeToSelect('*');

	if(!$includeOutOfStock) {
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
	}

	$prodIds = $products->getAllIds($paging_count, $paging_start);


	//var_dump(array_search('1016', $prodIds));exit;

	$paging_total = count($prodIds);

	$reviews = Mage::getModel('review/review_summary')->setStoreId($storeId);

	$mainloop = 0;

	$startTime = microtime(true);

	//Looping through the product ids to build the XML output
	foreach($prodIds as $productId) {

		//Initializing the product object
		$product = Mage::getModel('catalog/product')->load($productId);

		$pricing = getPricing($product);

		$product_price = number_format($pricing['lowestPrice'], 2, '.', '');
		$product_regularprice = number_format($pricing['regularPrice'], 2, '.', '');
		$product_normalprice = number_format($pricing['normalPrice'], 2, '.', '');

		if(!$includeZeroPrice && $product_price == 0) {
			continue;
		}

		//Preparing the custom field arrays
		$custom_field_names = array();
		$custom_field_values = array();

		//Populating the product fields
		$product_typeid = $product->getTypeId();
		$product_weight = $product->getWeight();
		$product_quantity = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
		$product_isinstock = $product->isInStock();
		$product_shortdesc = $product->getShortDescription();
		$product_model = $product->getSku();
		$product_name = $product->getName();
		$product_desc = $product->getDescription();
		$product_attribute_id = $product->getAttributeSetId();
		$product_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getUrlPath();
		$product_image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage();
		$product_thumbnail = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getThumbnail();
		$product_cached_thumbnail = Mage::helper('catalog/image')->init($product, 'image')->resize(200);

		//Getting the manufacturer data
		if ($product->getResource()->getAttribute('manufacturer')) {
			$manufacturer = $product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product);
		} else {
			$manufacturer = "";
		}


		//Stripping out unwanted characters from product name
		$product_name = str_replace("\n", "", strip_tags($product_name));
		$product_name = str_replace("\r", "", strip_tags($product_name));
		$product_name = str_replace("\t", " ", strip_tags($product_name));

		//Applying numeric formatting to applicable fields
		$product_weight = number_format($product_weight, 2, '.', '');
		$product_quantity  = number_format($product_quantity, 0, '.', '');

		$doc = new DOMDocument("1.0");
		$node = $doc->createElement("Product");
		$productElement = $doc->appendChild($node);

		addChildToDomElement($productElement, 'sku', $product_model);
		addChildToDomElement($productElement, 'product_type', $product_typeid);
		addChildToDomElement($productElement, 'name', $product_name);
		addChildToDomElement($productElement, 'manufacturer', $manufacturer);

		addChildToDomElement($productElement, 'url', $product_url);
		addChildToDomElement($productElement, 'image_url', $product_image);
		addChildToDomElement($productElement, 'thumbnail_url', $product_thumbnail);
		addChildToDomElement($productElement, 'cached_thumbnail_url', $product_cached_thumbnail);
		addChildToDomElement($productElement, 'quantity', $product_quantity);
		addChildToDomElement($productElement, 'in_stock', $product_isinstock);
		addChildToDomElement($productElement, 'description', preg_replace('/[^(\x20-\x7F)\x0A]*/','', $product_desc));
		addChildToDomElement($productElement, 'short_description', preg_replace('/[^(\x20-\x7F)\x0A]*/','', $product_shortdesc));


		//Getting the product categories in hierarchy format
		$category_hierarchy = array();
		$category_names = array();
		foreach($product->getCategoryIds() as $_categoryId){
			$category = Mage::getModel('catalog/category')->load($_categoryId);

			if(!$category->getIsActive()) {
				continue;
			}

			$path = array_reverse(explode(',', $category->getPathInStore()));

			$current_hierarchy = array();
			foreach($path as $current) {
				$category = Mage::getModel('catalog/category')->load($current);
				$current_hierarchy[] = $category->getName();

				$category_hierarchy[] = implode('/', $current_hierarchy);
			}

			$category_names[] = $category->getName();

		}
		$category_hierarchy = array_unique($category_hierarchy);


		foreach($category_hierarchy as $category_hierarchy) {
			addChildToDomElement($productElement, 'category_hierarchy', $category_hierarchy);
		}

		foreach($category_names as $category_name) {
			addChildToDomElement($productElement, 'category', $category_name);
		}

		foreach ($product->getAttributes(null, true) as $attribute) {
			if ($attribute != null && $attribute->usesSource()) {
				$custom_field_value = $product->getAttributeText($attribute->getAttributeCode());
			} else {
				$custom_field_value = $product->getData($attribute->getAttributeCode());
			}


			if (!empty($custom_field_value)) {
				$tag = str_replace(" ", "", $attribute->getAttributeCode());

				// We don't want to include fields we already have
				if(in_array($tag, $core_fields)) {
					continue;
				}

				if (is_array($custom_field_value)) {
					foreach ($custom_field_value as $v) {
						if (!is_array($v)) {
							addChildToDomElement($productElement, $tag, $v);
						}
					}
				} else {
					// Strip invalid characters
					$custom_field_value = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $custom_field_value);
					addChildToDomElement($productElement, $tag, $custom_field_value);
				}
			}
		}



		$product_child_qty = 0;
		// Get configurable product attributes
		if($product->isConfigurable()) {
			$attributeObjects = $product->getTypeInstance(true)->getConfigurableAttributes($product);
			$attributes = array();

			foreach($attributeObjects as $attribute) {
				$productAttribute = $attribute->getProductAttribute();
				if($productAttribute) {
					$attributes[] = array(
						'values'         => $attribute->getPrices() ? $attribute->getPrices() : array(),
						'attribute_code' => $attribute->getProductAttribute()->getAttributeCode(),
					);
				}
			}
			
			foreach($attributes as $attribute) {
				$tag = str_replace(" ", "", $attribute['attribute_code']);
				foreach($attribute['values'] as $value) {
					addChildToDomElement($productElement, $tag, $value['label']);
				}
			}

			$children = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
			foreach($children as $child) {
				addChildToDomElement($productElement, 'child_sku', $child->getSku());
				addChildToDomElement($productElement, 'child_name', $child->getName());

				$qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
				$product_child_qty += $qty;
			}
		} else if($product->isGrouped()) {
			$children = $product->getTypeInstance(true)->getAssociatedProducts($product);
			foreach($children as $child) {
				addChildToDomElement($productElement, 'child_sku', $child->getSku());
				addChildToDomElement($productElement, 'child_name', $child->getName());

				$qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
				$product_child_qty += $qty;
			}
		} else if($product->getTypeId() == 'bundle') {
			$options = $product->getTypeInstance(true)->getChildrenIds($product->getId(), true);
			foreach($options as $children) {
				foreach($children as $child) {
					$qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
					$product_child_qty += $qty;

					$child = Mage::getModel('catalog/product')->load($child);
					addChildToDomElement($productElement, 'child_sku', $child->getSku());
					addChildToDomElement($productElement, 'child_name', $child->getName());
				}
			}
		} else {
			// simple product?
			// check if product has a parent
			$parent_id = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
	    if(!$parent_id)
	        $parent_id = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
	    if(isset($parent_id[0])){
	        $parent = Mage::getModel('catalog/product')->load($parent_id[0]);
	        $parent_sku = $parent->getSku();
	        addChildToDomElement($productElement, 'parent_sku', $parent_sku);
	    }
			// If the product has no children just use the actual quantity
			$product_child_qty = $product_quantity;
		}

		addChildToDomElement($productElement, 'child_quantity', number_format($product_child_qty, 0, '.', ''));


		addChildToDomElement($productElement, 'price', $product_price);
		addChildToDomElement($productElement, 'regular_price', $product_regularprice);
		addChildToDomElement($productElement, 'normal_price', $product_normalprice);


		$rating = Mage::getModel('review/review_summary')->setStoreId($storeId)->load($product->getId());

		//For probiotic multi-store
		//$rating = Mage::getModel('review/review_summary')->setStoreId(Mage::app()->getStore($product->getAttributeText('site'))->getStoreId())->load($product->getId());

		if($rating->getId()) {
			$ratingPct = $rating['rating_summary'];
			$ratingStar = round($ratingPct / 20);
			$ratingCount = $rating['reviews_count'];
			addChildToDomElement($productElement, 'rating_percentage', $ratingPct);
			addChildToDomElement($productElement, 'rating_star', $ratingStar);
			addChildToDomElement($productElement, 'rating_count', $ratingCount);
		} else {
			addChildToDomElement($productElement, 'rating_percentage', 0);
			addChildToDomElement($productElement, 'rating_star', 0);
			addChildToDomElement($productElement, 'rating_count', 0);
		}


		// Get Custom Options
		foreach ($product->getOptions() as $o) {
			$optionType = $o->getType();
			if ($optionType == 'drop_down') {
				$tag = 'option_' . strtolower(preg_replace('/_+/', '_', preg_replace('/[^a-z0-9_]+/i', '_', trim($o->getTitle()))));

				$values = $o->getValues();
				foreach ($values as $k => $v) {
					$custom_field_value = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $v->getTitle());
					addChildToDomElement($productElement, $tag, $custom_field_value);
				}
			}
		}


		$xml = $productElement->ownerDocument->saveXML($productElement);
		fwrite($fh, $xml);

		// Cleanup any data before proceeding to the next product
		cleanup($product);
		unset($product);


	}

}
catch(Exception $e){
	die($e->getMessage());
}


if ($paging_total < $paging_count) {

	$xml = "</Products>\n";
	fwrite($fh, $xml);
	fclose($fh);

	rename($outputFile, 'searchspring_'.$store.'.xml');

	echo "Complete\n";


} else {
	echo 'Continue|' . ($paging_start + $paging_total) . "\n";
}

if (is_resource($fh)) {
	fclose($fh);
}

//////////////

//Scrubbing various unwanted characters
function scrubData($fieldValue)
{
	$fieldValue = str_replace(chr(10), " ", $fieldValue);
	$fieldValue = str_replace(chr(13), " ", $fieldValue);
	$fieldValue = str_replace("\r", " ", $fieldValue);
	$fieldValue = str_replace("\n", " ", $fieldValue);
	$fieldValue = str_replace("\r\n", " ", $fieldValue);
	$fieldValue = str_replace("\t", "    ", $fieldValue);
	$fieldValue = str_replace("™", "&trade;", $fieldValue);
	$fieldValue = str_replace("•", "", $fieldValue);
	$fieldValue = str_replace("", "", $fieldValue);
	$fieldValue = str_replace("<![CDATA[", "", $fieldValue);
	$fieldValue = str_replace("]]>", "", $fieldValue);
	$fieldValue = str_replace("]]", "", $fieldValue);
	$fieldValue = strip_tags($fieldValue);
	return $fieldValue;
}

function addChildToDomElement(&$domElem, $name, $value) {
	$node = $domElem->ownerDocument->createElement($name, htmlspecialchars($value));
	$domElem->appendChild($node);
}

function getPricing($_product) {

	$childPrices = array();
	$salePrices = array();
	$tierPrices = array();
	if($_product->getTypeId() == 'grouped') {
		$children = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
		foreach($children as $child) {
			$childPrices[] = $child->getPrice();
			$salePrices[] = $child->getFinalPrice();
			$tier_prices = $child->getTierPrice();
			foreach($tier_prices as $tier_price) {
				if($tier_price['cust_group'] !== '4') {
					$tierPrices[] = (double)$tier_price['price'];
				}
			}
		}
	} else if ($_product->getTypeId() == 'bundle') {
		$priceModel = $_product->getPriceModel();
		$block = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
		$options = $block->setProduct($_product)->getOptions();

		$price = 0;
		$lowest_price = 0;
		foreach($options as $option) {
			$selections = $option->getSelections();
			$lowest_price_option = 0;
			foreach($selections as $selection) {
				$option_price = $priceModel->getSelectionPreFinalPrice($_product, $selection, $selection->getSelectionQty());

				if($selection->getIsDefault()) {
					$price += $option_price;
				}

				if($option->getRequired()) {
					if($option_price < $lowest_price_option || $lowest_price_option == 0) {
						$lowest_price_option = $option_price;
					}
				}
			}
			$lowest_price += $lowest_price_option;
		}

		if($price == 0) {
			$price = $_product->getFinalPrice();
		}

		if($lowest_price == 0) {
			$lowest_price = $_product->getFinalPrice();
		}

		return array(
			'regularPrice' => $price,
			'lowestPrice' => $lowest_price
		);
	} else {
		$tier_prices = $_product->getTierPrice();
		foreach($tier_prices as $tier_price) {
			if($tier_price['cust_group'] !== '4') {
				$tierPrices[] = (double)$tier_price['price'];
			}
		}
	}

	$minTierPrice = 0;
	if(!empty($tierPrices)) {
		$minTierPrice = min($tierPrices);
	}

	$minChildPrice = 0;
	if(!empty($childPrices)) {
		$minChildPrice = min($childPrices);
	}

	$minSalePrice = 0;
	if(!empty($salePrices)) {
		$minSalePrice = min($salePrices);
	}

	$minPrice = $_product->getMinimalPrice();
	$regPrice = $_product->getPrice();
	$salePrice = $_product->getFinalPrice();

	$prices = array();
	$salePrices = array();

	if(!empty($minPrice)) $prices[] = $minPrice;
	if(!empty($minTierPrice)) $prices[] = $minTierPrice;
	if(!empty($regPrice)) $prices[] = $regPrice;
	if(!empty($minChildPrice)) $prices[] = $minChildPrice;

	$lowestRegularPrice = min($prices);

	if(!empty($salePrice)) {
		$prices[] = $salePrice;
	}

	if(!empty($minSalePrice)) {
		$prices[] = $minSalePrice;
	}

	$lowestPrice = min($prices);

	return array(
		'regularPrice' => $lowestRegularPrice,
		'lowestPrice' => $lowestPrice,
		'normalPrice' => $regPrice
	);
}

function cleanup($product) {

	// An unfortunate bug in magento caches configurable product attribute
	// data in the type instance singelton, affecting all other products
	// following the call. This affects all configurable products. The
	// property we need to clear is protected, so we'll use the reflection
	// library to reset the value.
	$type = $product->getTypeInstance(true);
	$reflectionClass = new ReflectionClass($type);
	$property = $reflectionClass->getProperty('_editableAttributes');
	$property->setAccessible(true);
	$property->setValue($type, null);

}