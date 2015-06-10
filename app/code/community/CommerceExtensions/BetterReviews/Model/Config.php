<?php

class CommerceExtensions_BetterReviews_Model_Config extends Mage_Core_Model_Abstract
{
  const XML_PATH_PRODUCTPAGEREVIEWS_ENABLED           = 'productpagereviews/general/enabled';
  const XML_PATH_UPDATE_BLOCK_TITLE_ENABLED           = 'productpagereviews/general/update_block_title';
  const XML_PATH_UPDATE_BLOCK_TITLE_PATTERN_SIMPLE    = 'productpagereviews/general/block_title_pattern_simple'; // applies to all types of product except grouped & configurable  
  const XML_PATH_UPDATE_BLOCK_TITLE_PATTERN_GROUPED   = 'productpagereviews/general/block_title_pattern_grouped'; // applies to grouped and configurable
  const XML_PATH_REVIEWS_SORT_ORDER                   = 'productpagereviews/general/list_order';
  const XML_PATH_REVIEWS_SORT_DIR                     = 'productpagereviews/general/list_dir';
  const XML_PATH_RICHSNIPPETS_ENABLED                 = 'productpagereviews/general/richsnippets_enabled';
  const REVIEWS_HASH_SUFFIX                           = '#customer-reviews';
  
  const XML_PATH_ASSOCIATED_PRODUCT_REVIEWS_ENABLED   = 'associatedproductreviews/general/enabled';
  const XML_PATH_GROUPED_PRODUCT_REVIEWS_ENABLED      = 'associatedproductreviews/general/grouped_enabled';
  const XML_PATH_CONFIGURABLE_PRODUCT_REVIEWS_ENABLED = 'associatedproductreviews/general/configurable_enabled';    
  const XML_PATH_ASSOCIATED_PRODUCT_ATTRIBUTE_ENABLED = 'associatedproductreviews/general/attribute_enabled';
  const XML_PATH_ASSOCIATED_PRODUCT_ATTRIBUTE         = 'associatedproductreviews/general/attribute';
}