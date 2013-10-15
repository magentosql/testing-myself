<?php


  global $xml;


function AddNode($name, $value)
{

global $xml;

$value=str_replace("\n", " ", $value);

$value=str_replace("\r", " ", $value);

$xml.="<$name>";

$xml.=$value;

$xml.="</$name>";

return;

}

  header('Content-type: text/xml');




define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_USER', 'thelaund_magento');
define('DB_PASSWORD', 'ElsePipesCartMounts72');
define('DB_NAME', 'thelaund_magento');
define('DB_PREFIX', '');
 
 
 
 

$db_connection=mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);


mysql_select_db(DB_NAME,$db_connection);

global $orderid;

$firstinv=$_POST["inv"];

if(strlen($firstinv)==0)
$firstinv=0;





//if($firstinv=="")
//$firstinv=$_GET["inv"];

//(status='Complete') and
 
 
 $it_select="SELECT * from sales_flat_order where entity_id > '$firstinv' order by entity_id limit 0,50";
 
 
$query_result=mysql_query($it_select, $db_connection);

 
 

$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><orders>";

 

while($row = mysql_fetch_object($query_result))
{
	
	$xml.="<order>";
 
	AddNode("customer_id", $row->customer_id);

	$orderid=$row->entity_id;

	AddNode("order_id", $row->entity_id);

	$billing_address_id=$row->billing_address_id;

	$shipping_address_id=$row->shipping_address_id;


 

	$sql="select * from sales_flat_order_address where entity_id=$billing_address_id";
	$qr=mysql_query($sql);

	if($row2=mysql_fetch_object($qr))
	{
	$xml.="<billing_address>";

	$contact=$row2->firstname." ".$row2->lastname;
 
	AddNode("name", $contact);

	AddNode("company", $contact);

	
	AddNode("address1", $row2->street);


	AddNode("city", $row2->city);


	AddNode("state", $row2->region);


		AddNode("zip", $row2->postcode);
		AddNode("country", $row2->country_id);

		AddNode("phone", $row2->telephone);

		AddNode("fax", $row2->fax);

		AddNode("email", $row2->email);

		AddNode("firstname", $row2->firstname);
		AddNode("lastname", $row2->lastname);




		$xml.="</billing_address>";
	} 

	AddNode("ship_method", $row->shipping_method);
 
 

	AddNode("po", $row->increment_id);

	AddNode("order_date", $row->created_at);

	AddNode("subtotal", $row->base_subtotal);
 
 
	if($shipping_address_id!="")
	{
 

	$sql="select * from sales_flat_order_address where entity_id=$shipping_address_id";
	$qr=mysql_query($sql);

	if($row2=mysql_fetch_object($qr))
	{
		
	$xml.="<shipping_address>";
 
	AddNode("name", $row2->firstname." ".$row2->lastname);

	AddNode("company", $row2->firstname." ".$row2->lastname);

	AddNode("address1", $row2->street);


	AddNode("city", $row2->city);


	AddNode("state", $row2->region);


		AddNode("zip", $row2->postcode);
		AddNode("country", $row2->country_id);

		AddNode("phone", $row2->telephone);

		AddNode("fax", $row2->fax);

		AddNode("email", $row2->email);

		AddNode("firstname", $row2->firstname);
		AddNode("lastname", $row2->lastname);




		$xml.="</shipping_address>";

	} 

	}///there is a shipping address id
 
		AddNode("freight", $row->shipping_incl_tax);
 

		AddNode("promo", $row->giftcert_code);

		

		$gmi=$row->gift_message_id;

		if($gmi!="")
		{

		$sql="select * from gift_message where gift_message_id = ".$gmi;
		$qr2=mysql_query($sql);
		if($row2=mysql_fetch_object($qr2))
		AddNode("comments", $row2->message);

		}

 
 
		AddNode("status", $row->status);
 

		$group_id=$row->customer_group_id;
		$sql="select * from customer_group where customer_group_id=$group_id";
		$query_result1=mysql_query($sql, $db_connection);

		if($row2 = mysql_fetch_object($query_result1))
		AddNode("order_class", $row2->customer_group_code);
 
 
		AddNode("tax", $row->base_tax_amount);
 

 		AddNode("source", $row->store_name);
 

		$sql2="select * from sales_flat_order_payment where entity_id=$orderid";

		$query_result3=mysql_query($sql2, $db_connection);

		$row2 = mysql_fetch_object($query_result3);
		{
		$method=str_replace("\n", "", $row2->method);

		AddNode("payment_method", $row2->method);

		}

		$sql2="select * from sales_flat_quote_payment where payment_id=$orderid";

		$query_result3=mysql_query($sql2, $db_connection);

		$row2 = mysql_fetch_object($query_result3);
		{
		$method=str_replace("\n", "", $row2->cc_type);

		AddNode("cc_type", $row2->cc_type);

		}

 
		$xml.="<line_items>";

		$sql="select * from sales_flat_order_item where order_id=$orderid";

		$query_result1=mysql_query($sql, $db_connection);

		$total=0;

		while($row2 = mysql_fetch_object($query_result1))
		{
				$xml.="<line_item>";

				$sku=$row2->sku;
 
 
				AddNode("sku", $sku);

				AddNode("description", substr($row2->name, 0, 95));
 
				AddNode("price", $row2->base_price-$row2->discount_amount);
	 
				AddNode("qty", $row2->qty_ordered);

				AddNode("discount", $row2->discount_amount);

				$xml.="</line_item>";
	 

		}

		$xml.="</line_items></order>";




		//$sql="update sales_flat_order set status='Closed' where entity_id=$orderid";
		//mysql_query($sql, $db_connection);

	}

	$xml.="</orders>";

	print $xml;

		 


?>
