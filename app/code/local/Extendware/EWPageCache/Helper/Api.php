<?php /*obfv1*/
// Copyright © 2015 Extendware
// Are you trying to customize your extension? Contact us (http://www.extendware.com/contacts/) and we can help!
// Please note, not all files are encoded and different extensions have different levels of encoding.
// We are always happy to provide guideance if you are experiencing an issue!



/**
 * Below are methods found in this class
 *
 * @method mixed public __construct()
 * @method mixed public addCategoryIdsAsTagsForSave(array $ids = array())
 * @method mixed public addCmsPageIdsAsTagsForSave(array $ids = array())
 * @method mixed public addProductIdsAsTagsForSave(array $ids = array())
 * @method mixed public addTagsForSave(array $tags)
 * @method mixed public cleanCache($mode = Zend_Cache::CLEANING_MODE_ALL, array $tags = array(), $realTime = null, $flushBlockCache = null)
 * @method mixed public cleanLighteningCache()
 * @method mixed public flushPagesByGroup(array $groups = array(), array $storeIds = array())
 * @method mixed public flushPagesMatchingAnyCacheKey(array $keys)
 * @method mixed public flushPagesMatchingAnyTag(array $tags, $realTime = null, $clearCache = false)
 * @method mixed public getCacheBackend()
 * @method mixed public getCategoryIdsFromProductIds(array $ids)
 * @method mixed public getLinkedProductIdsFromProductIds(array $ids = array(), array $typeIds = array())
 * @method mixed public getParentCategoryIdsFromCategoryIds(array $ids)
 * @method mixed public getParentProductIdsFromProductIds(array $ids = array())
 * @method mixed public getTagsForFlushFromCategoryIds(array $ids, $type = 'default', $clearCache = false)
 * @method mixed public getTagsForFlushFromCmsPageIds(array $ids, $type = 'default', $clearCache = false)
 * @method mixed public getTagsForFlushFromProductIds(array $ids, $type = 'default', $clearCache = false)
 * @method mixed public getTagsFromCategoryIds(array $ids)
 * @method mixed public getTagsFromCmsPageIds(array $ids)
 * @method mixed public getTagsFromProductIds(array $ids)
 * @method mixed public setCacheLifetimeForSave($seconds)
 * @method mixed public setIgnoreFlushes($bool)
 * @method mixed public setPageIsCacheable($bool)
 * @method mixed public setWillBeLighteningCacheLoadable($bool)
 * @method mixed public setWillBePrimaryCacheLoadable($bool)
 *
 */

$_F=__FILE__;$_X="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";$_D=strrev("edoced" . "_46esab");eval(gzuncompress($_D($_X)));
