<?php

class Wsnyc_QuestionsAnswers_Model_Observer {
    
    public function addQuestionsToSitemap(Varien_Event_Observer $observer) {
        
        $io = $observer->getEvent()->getIo();
        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        
        $changefreq = (string)Mage::getStoreConfig('sitemap/asklaundress/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/asklaundress/priority', $storeId);
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>0));
        foreach ($collection as $item) {
            foreach($item->getSubcategoriesCollection() as $_item) {
                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars(str_replace('/index.php', '', $_item->getUrlRewrite())),
                    $date,
                    $changefreq,
                    $priority
                );
                $io->streamWrite($xml);
            }
        }
        unset($collection);
        
    }
}