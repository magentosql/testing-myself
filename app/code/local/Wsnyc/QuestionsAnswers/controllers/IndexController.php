<?php
class Wsnyc_QuestionsAnswers_IndexController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * Category Listing
     */
    public function indexAction ()
    {
        $this->loadLayout();
        $this->renderLayout();

    }

}