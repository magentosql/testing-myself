<?php

require 'app/Mage.php';
Mage::init();
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);

if (($handle = fopen("categories.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;

        $subcategoryname = trim($data[0]);
        $categoryname = trim($data[1]);


        $cat = Mage::getModel('wsnyc_questionsanswers/category')->load($categoryname, 'name');

        $categoryCollection = Mage::getModel('wsnyc_questionsanswers/category')->getCollection();
        $categoryCollection->addFieldToFilter('name',array('eq'=>trim($categoryname)));
        $categoryCollection->addFieldToFilter('parent_id',array('eq'=>0));



        if ($categoryCollection->count()) {
            $cat = $categoryCollection->getFirstItem();
            $categoryId = $cat->getId();
        } else {
            $category = Mage::getModel('wsnyc_questionsanswers/category')->setName($categoryname)->save();
            $categoryId = $category->getId();
        }

        $categoryCollection = Mage::getModel('wsnyc_questionsanswers/category')->getCollection();
        $categoryCollection->addFieldToFilter('name',array('eq'=>trim($subcategoryname)));
        $categoryCollection->addFieldToFilter('parent_id',array('eq'=>$categoryId));
        if(!$categoryCollection->count()){
            Mage::getModel('wsnyc_questionsanswers/category')
                ->setName($subcategoryname)
                ->setParentId($categoryId)
                ->save();
        }
    }
    fclose($handle);
}


if (($handle = fopen("questions.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;

        $date = $data[0];
        $name = trim($data[1]);
        $questionText = $data[2];
        $answerText = $data[3];
        $recommendedProduct = $data[4];
        $category = trim($data[5]);
        $subcategory = trim($data[6]);


        //getcategory
        $categoryCollection = Mage::getModel('wsnyc_questionsanswers/category')->getCollection();
        $categoryCollection->addFieldToFilter('name',array('eq'=>trim($category)));
        $categoryCollection->addFieldToFilter('parent_id',array('eq'=>0));
        if ($categoryCollection->count()) {
            $cat = $categoryCollection->getFirstItem();
            $categoryId = $cat->getId();
        }
        //getsubcategoryId
        $subcategoryId = 0;
        $categoryCollection = Mage::getModel('wsnyc_questionsanswers/category')->getCollection();
        $categoryCollection->addFieldToFilter('name',array('eq'=>trim($subcategory)));
        $categoryCollection->addFieldToFilter('parent_id',array('eq'=>$categoryId));
        if ($categoryCollection->count()) {
            $cat = $categoryCollection->getFirstItem();
            $subcategoryId = $cat->getId();
        }

        $question = Mage::getModel('wsnyc_questionsanswers/question');
        $question->setCategoryId($subcategoryId)
            ->setQuestionText($questionText)
            ->setAskedName($name)
            ->setCreatedAt(date("Y-m-d H:i:s", strtotime($date)))
            ->setPublished(1)
        ->save();
        
        $products = explode(',',$recommendedProduct);
        foreach ($products as $product){
        try{
	    $qp = Mage::getModel('wsnyc_questionsanswers/questionproduct');
	    $qp->setQuestionId($question->getQuestionId())
	    ->setProductSku(trim($product))
	    ->setCreatedAt(date("Y-m-d H:i:s"))
	    ->save();
	    }catch(Exception $e){
	    var_dump($e);
	    }
        }

        $answer = Mage::getModel('wsnyc_questionsanswers/answer')->setQuestionId($question->getQuestionId())->setAnswerText($answerText)->save();
    }
    fclose($handle);
}

