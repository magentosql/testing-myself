<?php 

class Wsnyc_Shippingnote_Model_Observer{

    public function salesOrderSaveBefore($observer){
        $order = $observer->getOrder();
        $signatureSurvey = Mage::app()->getFrontController()->getRequest()->getParams();
        $selectedKey = $signatureSurvey['billing']['onestepcheckout-survey'];
        $laundressComment = $signatureSurvey['billing']['onestepcheckout_comment'];

        $possibleSignatureValues = Mage::getStoreConfig('onestepcheckout/survey/survey_values');
        $possibleSignatureValues = unserialize($possibleSignatureValues);
        
        $selectedAnswer = strtolower($possibleSignatureValues[$selectedKey]['value']);
        if($selectedAnswer=='yes'){ 
                $order->setSignatureRequired(1);
        } else { 
                $order->setSignatureRequired(0);
        }
        $order->setOnestepcheckoutLaundressComment($laundressComment);
                
    }

}