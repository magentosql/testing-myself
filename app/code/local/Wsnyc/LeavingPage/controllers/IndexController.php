<?php

class Wsnyc_LeavingPage_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $iClient = new Varien_Http_Client();
        $iClient->setUri('http://thelaundress.us6.list-manage.com/subscribe/post?u=d3d48e75efd637e646b0beb3c&amp;id=dbfb7e7934')
            ->setMethod('POST')
            ->setConfig(array(
                'maxredirects' => 0,
                'timeout' => 30,
            ));
        $params = $this->getRequest()->getParams();
        $params['submit'] = 'Subscribe';
        $iClient->setRawData(http_build_query($params));
        $response = $iClient->request();
        $str = $response->getBody();
        if (strstr($str, 'Almost finished...')) {
            $content = Mage::app()->getLayout()->createBlock('core/template', 'leavingpage-redeem', array('template' => 'wsnyc/leavingpage/redeem.phtml', 'cms_block' => Mage::getModel('cms/block')->load('pageleave-modal-redeem', 'identifier')))->toHtml();
            $_response = array('success' => true, 'content' => $content);
        } else {
            $doc = new DOMDocument();
            @$doc->loadHTML($str);
            $finder = new DomXPath($doc);
            $classname = "errorText";
            $nodes = $finder->query("//*[contains(@class, '$classname')]");

            foreach ($nodes as $node) {
                $msg = (string)$node->nodeValue;
                $cut = strpos($msg, 'Click here');
                $msg = $cut > - 1 ? substr($msg, 0, $cut) : $msg;
            }
            $_response = array('success' => false, 'message' => $msg);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_response));
    }
}
