<?php
class Wsnyc_QuestionsAnswers_Model_Category extends Mage_Core_Model_Abstract {
    
    protected $_eventPrefix = 'questionsanswers_category';

    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/category');
    }

    public function getQuestionsCollection(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $collection->addFieldToFilter('category_id',array('eq'=>$this->getCategoryId()));
        return $collection;
    }

    public function getSubcategoriesCollection(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>$this->getCategoryId()));
        return $collection;
    }

    /**
     * Normal url - not seo-wise
     *
     * @return string
     */
    public function getUrl(){

        return Mage::getUrl(
            'asklaundress/questions/index/',
            array(
                'id' => $this->getCategoryId(),
            )
        );
    }

    /**
     * Returns SEO url, with fallback to normal one
     * @return string
     */
    public function getUrlRewrite()
    {
        $id_path = 'wsnyc_questionsanswers/category/'.$this->getCategoryId();

        /* @var $mainUrlRewrite Mage_Core_Model_Url_Rewrite */
        $mainUrlRewrite = Mage::getModel('core/url_rewrite')
            ->loadByIdPath($id_path);


        // If found return our URL rewrite
        if ($mainUrlRewrite->getId())
        {
            return Mage::getUrl($mainUrlRewrite->getRequestPath());
        }
        else
        {
            return $this->getUrl();
        }
    }



    /**
     * Create the SEO url rewrite. If the identifier is empty,
     * nothing is done, otherwise these are the steps taken:
     *
     * - get the current main url rewrite
     * - see if there are currently redirects to the main url rewrite
     * + if there are, redirect those to the new identifier
     * + should one have the same url as the new identifier, it will be deleted
     * - change the main url rewrite to use the new identifier
     * - make a permament redirect from the old to the new identifier if needed
     */
    protected function handleUrlRewrite()
    {
        $id_path = "wsnyc_questionsanswers/category/{$this->getCategoryId()}";
        $request_path = "asklaundress/{$this->getIdentifier()}";
        $target_path = "asklaundress/questions/index/id/{$this->getCategoryId()}";

        /**
         * First, check if there is already a main url rewrite object.
         */
        /* @var $mainUrlRewrite Mage_Core_Model_Url_Rewrite */
        $mainUrlRewrite = Mage::getModel('core/url_rewrite')
            ->loadByIdPath($id_path);
        /**
         * If there already is a main url rewrite object, check if there are
         * redirects to this one.
         */
        if (!$mainUrlRewrite->isObjectNew())
        {
            $urlRewriteCollection = Mage::getModel('core/url_rewrite')->getCollection()
                ->addFilter('target_path', $mainUrlRewrite->getRequestPath())
                ->addFieldToFilter('url_rewrite_id', array('neq' => $mainUrlRewrite->getUrlRewriteId()))
                ->load();
            /**
             * If there are objects found, those must be redirected to the new
             * request_path.
             */
            foreach ($urlRewriteCollection as $urlRewrite)
            {
                /**
                 * Remove those object where the request path equals the current target
                 * path. This can occur if the user changes the url key back to
                 * an old one.
                 */
                if ($urlRewrite->getRequestPath() == $request_path)
                {
                    $urlRewrite->delete();
                } else
                {
                    /**
                     * Options are explicitly set to RP and system is set to 1.
                     * This way, the user knows it's changed by the system.
                     */
                    /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */
                    $urlRewrite->setTargetPath($request_path)
                        ->setIsSystem(true)
                        ->setOptions('RP')
                        ->save();
                }
            }
        }
        /**
         * Populate mainUrlRewrite with all data and save it. This way, for new
         * objects, an Url rewrite is created too.
         */
        $mainUrlRewrite->setIdPath($id_path)
            ->setRequestPath($request_path)
            ->setTargetPath($target_path)
            ->setIsSystem(true)
            ->save();
        /**
         * Check if a redirect must be made.
         */
        $identifier_create_redirect = $this->getData('identifier_create_redirect');
        if (!empty($identifier_create_redirect))
        {
            /**
             * A permanent redirect to the new url must be made.
             */
            $rewrite = Mage::getModel('core/url_rewrite');
            $rewrite->setIdPath("wsnyc_questionsanswers/category/{$this->getCategoryId()}_{$identifier_create_redirect}")
                ->setRequestPath("wsnyc_questionsanswers/{$identifier_create_redirect}")
                ->setTargetPath($request_path)
                ->setIsSystem(true)
                ->setOptions('RP')
                ->save();
        }
    }
    protected function _afterSave()
    {
        parent::_afterSave();
        $identifier = $this->getIdentifier();

        if (!empty($identifier))
        {
            $this->handleUrlRewrite();
        }
    }
    /**
     * Delete all url rewrites for this object.
     */
    protected function deleteUrlRewrites()
    {
        $id_path = "wsnyc_questionsanswers/category/{$this->getCategoryId()}";
        $mainUrlRewrite = Mage::getModel('core/url_rewrite')
            ->loadByIdPath($id_path);
        /**
         * If there is a main url rewrite object, check if there are redirects
         * to this one which must be deleted.
         */
        if (!$mainUrlRewrite->isObjectNew())
        {
            $urlRewriteCollection = Mage::getModel('core/url_rewrite')->getCollection()
                ->addFilter('target_path', $mainUrlRewrite->getRequestPath())
                ->addFieldToFilter('url_rewrite_id', array('neq' => $mainUrlRewrite->getUrlRewriteId()))
                ->load();
            /**
             * If there are objects found, those must be deleted.
             */
            foreach ($urlRewriteCollection as $urlRewrite)
            {
                $urlRewrite->delete();
            }
        }
        $mainUrlRewrite->delete();
    }


    protected function _beforeDelete()
    {
        /**
         * The url rewrite objects must also be deleted.
         */
        $this->deleteUrlRewrites();
        return parent::_beforeDelete();
    }

    public function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z/]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

}