<?php
// Code to pull in WP Blogs

$filename = "searchspring_howto";

$outputFile = $filename.'.xml.tmp';

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

// create file anew for use
$fh = fopen($outputFile, 'w');
$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$xml .= "<Contents>\n";
fwrite($fh, $xml);
fclose($fh);

try {
	$xml = '';
	$fh = fopen($outputFile, 'a');

	// get the blog content out of WordPress
	$con = mysql_connect("localhost", "thelaund_magento", "ElsePipesCartMounts72");

	if(!mysql_select_db("thelaund_wp_previous", $con)){
		echo "Did not connect to DB";
	}

	$result = mysql_query("SELECT * FROM wp_posts WHERE post_type='post' AND post_status='publish'");

	while($r = mysql_fetch_array($result)) {
		$blog_id = "blog-" . $r['ID'];
		$blog_date = date("M j, Y", strtotime($r['post_date']));
		$blog_raw_date = $r['post_date'];
		$blog_content = $r['post_content'];
		$blog_title = $r['post_title'];
		$blog_excerpt = $r['post_excerpt'];
		$blog_url = "http://blog.thelaundress.com/wordpress/" . $r['post_name'] . "/";
		
		// get images
		$blog_matchy = preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"].*>/i', $r['post_content'], $blog_images);
		if ($blog_matchy > 0) $blog_image = $blog_images[1][0];
		else $blog_image = "";

		// replace wierd MS fonts
		$blog_title = removeChars($blog_title);
		$blog_content = removeChars($blog_content);

		$doc = new DOMDocument("1.0");
		$node = $doc->createElement("Content");
		$productElement = $doc->appendChild($node);
		
		addChildToDomElement($productElement, 'ss_type', 'wp-blog');
		addChildToDomElement($productElement, 'entity_id', $blog_id);
		addChildToDomElement($productElement, 'sku', $blog_id);
		addChildToDomElement($productElement, 'name', $blog_title);
		addChildToDomElement($productElement, 'url', $blog_url);
		addChildToDomElement($productElement, 'short_description', $blog_excerpt);
		addChildToDomElement($productElement, 'description', preg_replace('/[^(\x20-\x7F)\x0A]*/','', $blog_content));
		addChildToDomElement($productElement, 'image', $blog_image);
		addChildToDomElement($productElement, 'creation_date', $blog_date);
		addChildToDomElement($productElement, 'raw_date', $blog_raw_date);
		
		$xml = $productElement->ownerDocument->saveXML($productElement);
		fwrite($fh, $xml);
	}

	mysql_close($con);




	// collect data from the Magento Module
	Mage::app();

	$qCol = Mage::getModel('wsnyc_questionsanswers/question')->getCollection();
	$aCol = Mage::getModel('wsnyc_questionsanswers/answer')->getCollection();
	$cCol = Mage::getModel('wsnyc_questionsanswers/category')->getCollection();

	// Collect category data keyed by category Id
	$categories = array();
	foreach($cCol as $category) {
	    $categories[$category->getCategoryId()] = $category->getData();
	}

	// Collect answers keyed by question Id
	$questions = array();
	foreach($qCol as $question) {
	    $questions[$question->getQuestionId()] = $question->getData();
	}

	// gather the questions and all the data out of the other collections
	foreach($aCol as $answer) {

	    $aid = "qa-" . $answer->getAnswerId();
	    $qid = $answer->getQuestionId();
	    $cid = $questions[$qid]['category_id'];

	    if (empty($categories[$cid]['name'])) {
	        $qa_image = '';
	        $qa_url = "http://www.thelaundress.com/asklaundress/questions/index/#q" . $qid;
	    }
	    else {
	        if ($categories[$cid]['parent_id'] == 0) {
	            $qa_image = "http://www.thelaundress.com/media/wsnyc_questionsanswers/" . $categories[$cid]['image'];
	        } else {
	            $qa_image = "http://www.thelaundress.com/media/wsnyc_questionsanswers/" . $categories[$categories[$cid]['parent_id']]['image'];
	        }
	        $qa_url = "http://www.thelaundress.com/asklaundress/" . $categories[$cid]['identifier'] . "/#q" . $qid;
	    }

	    // add XML if question is not blank (duplicate question)
	    if (!empty($questions[$qid]['question_text']))
	    {
				$doc = new DOMDocument("1.0");
				$node = $doc->createElement("Content");
				$productElement = $doc->appendChild($node);
				
				addChildToDomElement($productElement, 'ss_type', 'mage-qa');
				addChildToDomElement($productElement, 'entity_id', $aid);
				addChildToDomElement($productElement, 'sku', $aid);
				addChildToDomElement($productElement, 'name', removeChars($questions[$qid]['question_text']));
				addChildToDomElement($productElement, 'url', $qa_url);
				addChildToDomElement($productElement, 'description', removeChars($answer->getAnswerText()));
				addChildToDomElement($productElement, 'image', $qa_image);
				addChildToDomElement($productElement, 'category', $categories[$cid]['name']);
				addChildToDomElement($productElement, 'parent_category', $categories[$cid]['parent_name']);
				
				$xml = $productElement->ownerDocument->saveXML($productElement);
				fwrite($fh, $xml);
	    }
	}


	// close the XML file
	$xml = "</Contents>\n";
	fwrite($fh, $xml);
	fclose($fh);
}
catch(Exception $e){
	die($e->getMessage());
}

rename($outputFile, $filename.'.xml');
echo "Complete\n";

function addChildToDomElement(&$domElem, $name, $value) {
	$node = $domElem->ownerDocument->createElement($name, htmlspecialchars($value));
	$domElem->appendChild($node);
}

function removeChars($stringy) {
	// replace wierd MS fonts
	// First, replace UTF-8 characters.
	$modstringy = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
		array("'", "'", '"', '"', '-', '--', '...'),
		$stringy);
	// Next, replace their Windows-1252 equivalents.
	$modstringy = str_replace(array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
		array("'", "'", '"', '"', '-', '--', '...'),
		$modstringy);
	return $modstringy;
}


?>