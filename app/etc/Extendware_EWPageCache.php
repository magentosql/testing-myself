<?php /*@copyright Extendware*/
return array('__system'=>array('status'=>1,'Extendware_EWPageCache'=>array('get_cache'=>array('spec'=>'8c8f5e3a98f385b1cf28def574e97f208ab9099b','data'=>array('get|is_primary_cache_enabled|0:stores'=>true,'get|is_secondary_cache_enabled|0:stores'=>true,'get|is_lightening_cache_enabled|0:stores'=>true,'get|lightening_cache_mode|0:stores'=>'simple','get|holepunch_mode|0:stores'=>'inline','get|username|0:stores'=>NULL,'get|password|0:stores'=>NULL,'get|cache_method|0:stores'=>'file','get|secondary_cache_method|0:stores'=>NULL,'get|cache_lifetime|0:stores'=>604800,'get|cache_directory|0:stores'=>'','get|database_index|0:stores'=>4,'get|is_compression_enabled|0:stores'=>true,'get|servers|0:stores'=>array(0=>array('host'=>'127.0.0.1','port'=>6379)),'get|is_injector_cache_enabled|0:stores'=>true,'get|injector_cache_lifetime|0:stores'=>300,'get|is_injector_compression_enabled|0:stores'=>true,'get|is_tagging_enabled|0:stores'=>true,'get|cms_auto_tagging_mode|0:stores'=>'lite','get|product_auto_tagging_mode|0:stores'=>'heavy','get|category_auto_tagging_mode|0:stores'=>'medium','get|tagging_block_tags|0:stores'=>array('catalog/product_new'=>array(0=>'newblock'),'catalog/product_widget_new'=>array(0=>'newblock')),'get|auto_flushing_mode|0:stores'=>'realtime','get|is_auto_flushing_block_cache_enabled|0:stores'=>true,'get|is_flush_parent_products_enabled|0:stores'=>true,'get|is_flush_parent_categories_enabled|0:stores'=>true,'get|flushing_custom_tags|0:stores'=>array('product_create'=>array(0=>'newblock'),'product_index_create_event'=>array(0=>'newblock'),'product_index_create_deferred_event'=>array(0=>'newblock')),'get|stock_quantity_change_mode|0:stores'=>'liberal','get|product_link_types_to_flush|0:stores'=>array(),'get|cms_auto_flushing_events|0:stores'=>array(0=>'cms_page_create',1=>'cms_page_update'),'get|product_auto_flushing_events|0:stores'=>array(0=>'product_create',1=>'product_update',2=>'product_delete',3=>'stock_quantity_changed',4=>'stock_state_changed',5=>'product_index_create_event',6=>'product_index_save_event',7=>'product_index_delete_event',8=>'product_index_mass_actions_event',9=>'stock_item_index_save_event',10=>'product_index_create_deferred_event',11=>'product_index_save_deferred_event',12=>'product_index_delete_deferred_event',13=>'product_index_mass_actions_deferred_event',14=>'stock_item_index_save_deferred_event',15=>'product_catalog_rule_apply'),'get|category_auto_flushing_events|0:stores'=>array(0=>'category_create',1=>'category_update',2=>'category_index_save_event',3=>'category_index_save_deferred_event'),'get|is_logging_enabled|0:stores'=>true,'get|is_logging_exclude_bad_requests_enabled|0:stores'=>true,'get|max_log_size|0:stores'=>2.5,'get|max_log_history|0:stores'=>100,'get|max_log_age|0:stores'=>5184000,'get|runnable_url_filters|0:stores'=>array(0=>'/(?:admin|ewaid|downloader|checkout)/',1=>'/thelaund_admin/',2=>'/(?:CrmTicket|ProductReturn|ProfitReport|MarketPlace|Amazon|OrderPreparation|SimpleBarcodeInventory|AdminLogger|Organizer|Scanner|Purchase|ExtensionConflict|SalesOrderPlanning|ClientComputer|sinchimport|customgrid|enhancedgrid|ordermanager|eparcel|pickpack|productupdater|attributemanager|AdvancedStock|BackgroundTask|htmlinvoice|deleteallorders|glsorderexport|megamenu|dashboardReviews|aitexporter|aitsys|mageworx|analyticsdash|intraship|optionextended|stockmanage|purolatorshipment|expeditorinet|profitlossreport|PointOfSales|customergroupsprice|pointsrelais|datafeedmanager|searchanise|magesetup|lexmeetsmage|updateemail|shipsync|Admin|aitcheckoutfields|eboekhouden)/'),'get|runnable_cookie_disqualifiers|0:stores'=>array(),'get|runnable_user_agent_disqualifiers|0:stores'=>array(),'get|primary_cache_reentry_types|0:stores'=>array(0=>'cart',1=>'login',2=>'compare'),'get|is_recently_viewed_products_enabled|0:stores'=>false,'get|is_exclude_bad_requests_enabled|0:stores'=>true,'get|is_record_product_views_enabled|0:stores'=>false,'get|caching_page_rules|0:stores'=>array('cms'=>array('active_virtual_keys'=>array('toolbar'=>'t')),'catalog-product-view'=>'
		
		
		
		
		
	','catalog-category-view'=>array('cache_lifetime'=>'','primary_cache'=>'1','active_virtual_keys'=>array('toolbar'=>'t')),'catalogsearch-result-index'=>'','newsletter-subscriber-unsubscribe'=>array('disable_cache'=>array('level'=>'primary')),'contacts-index-index'=>array('disable_cache'=>array('level'=>'secondary')),'ewquickview-product-view'=>'','ewlayerednav-navigation'=>array('active_virtual_keys'=>array('toolbar'=>'t')),'ewsearchsuggest-ajax-suggest'=>'','ewmasterpassword-customer-login'=>array('cacheable'=>'0','disable_cache'=>array('level'=>'primary')),'amcart-ajax-minicart'=>array('on_post'=>''),'__default_system'=>array('on_post'=>'disable_primary','virtual_keys'=>array('toolbar'=>array('alias'=>'t','model_params'=>array('order'=>array('key'=>'sort_order','model'=>'catalog/session'),'dir'=>array('key'=>'sort_direction','model'=>'catalog/session'),'mode'=>array('key'=>'display_mode','model'=>'catalog/session'),'limit'=>array('key'=>'limit_page','model'=>'catalog/session')))),'virtual_key_groups'=>array('toolbar_group'=>array('toolbarl'=>'t')),'active_virtual_keys'=>array()),'__default_match_system'=>array('cacheable'=>1,'logging'=>1,'primary_cache'=>1,'secondary_cache'=>1,'lightening_cache'=>1)),'get|caching_content_disqualifiers|0:stores'=>array(0=>'<!--ewpagecache-no-cache-->'),'get|defaultable_page_disqualifier_rules|0:stores'=>NULL,'get|segmentable_user_agents|0:stores'=>array('ewmobile'=>array(0=>'/ewmobile/'),'ewtablet'=>array(0=>'/ewtablet/')),'get|segmentable_cookies|0:stores'=>array('store'=>array('name'=>'store','expiry'=>NULL),'currency'=>array('name'=>'currency','expiry'=>NULL),'aw_mobile_version'=>array('name'=>'aw_mobile_version','expiry'=>NULL)),'get|injectors_list|0:stores'=>array('checkout/cart_sidebar'=>'checkout_cart_sidebar','catalog/product_compare_sidebar'=>'catalog_product_compare_sidebar','wishlist/customer_sidebar'=>'wishlist_customer_sidebar','reports/product_viewed'=>'reports_product_viewed','reports/product_compared'=>'reports_product_compared','poll/activePoll'=>'poll_activePoll','sales/reorder_sidebar'=>'sales_reorder_sidebar','wishlist/links'=>'wishlist_links','persistent/header_additional'=>'persistent_header_additional','checkout/cart_minicart'=>'checkout_cart_minicart'),'get|is_parameter_sorting_enabled|0:stores'=>true,'get|is_design_key_enabled|0:stores'=>false,'get|ignored_parameters|0:stores'=>array(0=>'form_key',1=>'___SID',2=>'SID',3=>'PHPSESSID',4=>'frontend',5=>'__no_primary_cache',6=>'__no_secondary_cache',7=>'gclid',8=>'__language',9=>'__currency',10=>'__open',11=>'__no_lightening_cache'),'get|translated_customer_groups|0:stores'=>array(1=>'0'),'get|tax_class_pieces|0:stores'=>array(),'get|cookie_settings|0:stores'=>array(1=>array('lifetime'=>'3600','httponly'=>'1','domain'=>'','path'=>''),2=>array('lifetime'=>'3600','httponly'=>'1','domain'=>'','path'=>'')),'get|is_secure_cookies_enabled|0:stores'=>false,'get|no_cache_cookie_name|0:stores'=>'epc-no-cache','get|no_primary_cache_cookie_name|0:stores'=>'epc-no-primary-cache','get|no_secondary_cache_cookie_name|0:stores'=>'epc-no-secondary-cache','get|cache_initiated_cookie_name|0:stores'=>'epc-initiated','get|primary_disabler_counter_cookie_name|0:stores'=>'epc-primary-disabler','get|session_param_name|0:stores'=>'SID','get|frontend_cookie_name|0:stores'=>'frontend','get|injector_cache_cookie_name|0:stores'=>'epc-ic','get|virtual_key_prefix|0:stores'=>'ewpcvc-','get|virtual_key_value_prefix|0:stores'=>'ewpcvcv-','get|developer_ip_rules|0:stores'=>array(),'get|is_output_headers_enabled|0:stores'=>true,'get|is_deleting_over_http_enabled|0:stores'=>true,'get|is_widget_enabled|0:stores'=>false,'get|is_footer_widget_enabled|0:stores'=>true,'get|is_hole_punch_hints_enabled|0:stores'=>false,'get|is_refresh_cache_sync_enabled|0:stores'=>false,'get|is_log_enabled|0:stores'=>false,'get|max_recently_viewed_products|0:stores'=>9,'get|page_max_ages|0:stores'=>array())))));