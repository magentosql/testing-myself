<?php

$installer = $this;

$content = <<<'EOT'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>The Laundress</title>
<meta name="viewport" content="width=740">
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<style type="text/css">
span {transition: color 450ms ease-in-out;-moz-transition: color 450ms ease-in-out;-webkit-transition: color 450ms ease-in-out;-o-transition: color 450ms ease-in-out}
a:hover span {color: #64b96a !important}
body {width:100% !important;text-rendering:optimizeLegibility;-webkit-font-smoothing:antialiased;}
.nav-table a {display: block;width: 100%;height: 100%;}
.nav-table td:hover {background-color: black}
.nav-table td:hover a {color: white !important}
</style>
<!--[if gte mso 9]>
<style type="text/css">
table,td,div,p {font-family: Arial, Helvetica, Verdana, sans-serif !important;line-height:normal !important}
</style>
<![endif]-->
<!--[if lte mso 7]>
<style type="text/css">
table,td,div,p {font-family: Arial, Helvetica, Verdana, sans-serif !important;line-height:normal}
</style>
<![endif]-->
</head>
<body bgcolor="#ffffff" style="margin:0;padding:0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td style="padding:20px 0 0 0;background-color:#ffffff">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
	<tr>
		<td align="center" style="padding-bottom:20px"><a href="http://www.thelaundress.com"><img src="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/logo.gif" alt="" width="230" height="45" border="0"></a></td>
	</tr>
	<tr>
		<td style="border-bottom: 4px solid #111111;border-top: 1px solid #f1f1f1">
			<table width="650" border="0" cellspacing="0" cellpadding="0" align="center" class="nav-table" style="margin:0 auto;-webkit-text-size-adjust:none;">
				<tr>
					<td align="center" height="34"><a style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;font-size:9px;line-height:34px;color:#222222;text-decoration:none" href="http://www.thelaundress.com/laundry-fabric-care">LAUNDRY & FABRIC CARE</a></td>
					<td align="center" height="34" style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;font-size:9px;line-height:34px"><a href="http://www.thelaundress.com/for-the-home" style="color:#222222;text-decoration:none">FOR THE HOME</a></td>
					<td align="center" height="34" style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;font-size:9px;line-height:34px"><a href="http://www.thelaundress.com/washing-service" style="color:#222222;text-decoration:none">WASHING SERVICE</a></td>
					<td align="center" height="34" style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;font-size:9px;line-height:34px"><a href="http://www.thelaundress.com/blog/ask-the-laundress/" style="color:#222222;text-decoration:none">ASK THE LAUNDRESS</a></td>
					<td align="center" height="34" style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;font-size:9px;line-height:34px"><a href="http://www.thelaundress.com/blog/" style="color:#222222;text-decoration:none">CLEAN TALK BLOG</a></td>
					<td align="center" height="34" style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;font-size:9px;line-height:34px"><a href="http://www.thelaundress.com/contact/" style="color:#222222;text-decoration:none">CONTACT</a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="650" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
	<tr>
		<td width="520">
			<table width="520" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
				<tr>
					<td width="520" colspan="2" style="padding-top:30px"><span style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;color:#000000;font-size:16px;text-transform:uppercase">Thanks for placing an order with The Laundress!</span></td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:20px"><span style="font-family:Georgia,serif;color:#999999;font-size:12px;font-style:italic">
For WHOLESALE orders, please allow 1-2 weeks for shipping with the exception of backorders. <br /><br />
ORDER MODIFICATIONS: All order modifications must be submitted within 24 hours of confirmed order receipt from The Laundress. <br /><br />
ORDER CANCELLATION: We can only accept cancellations of orders before midnight Eastern Standard Time on the same day the order is placed.<br /><br />
THE LAUNDRESS RETURN/DAMAGE POLICY: The Laundress does not accept any returns of their products. If your shipment has been damaged in transit, please contact us right away and either keep or photo-document the packaging for claims.<br /><br />
DEFECTS: Each delivery shall be conclusively deemed in accordance with The Laundress Terms & Conditions, unless within 24 hours of receiving the delivery Buyer notifies Seller of defective or incorrect merchandise. <br /><br />
If you have any questions, please e-mail <a href="mailto:sales@thelaundress.com">sales@thelaundress.com</a> or call us Monday-Friday 9am-5pm EST at 212-209-0074.
<br /><br>Check your inbox for a shipping confirmation email that includes your tracking number. </span>
                                        </td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:20px">
						<span style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#272727;font-size:13px;line-height:1.3em;text-transform:uppercase"><span style="font-weight:bold">Order Number</span>&#160;&#160;{{var order.increment_id}}<br>
						<span style="font-weight:bold">Date purchased</span>&#160;&#160;{{var order.getCreatedAtFormated('short')}}<br>
                                                <span style="font-weight:bold">Customer Email</span>&#160;&#160;{{var order.customer_email}}<br>
                                                <span style="font-weight:bold">Requires Signature</span>&#160;&#160;{{var order.getSignatureRequired()}}<br>
                                                <span style="font-weight:bold">Order Comment</span>&#160;&#160;{{var order.getOnestepcheckoutLaundressComment()}}</span>
					</td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:25px">
						<table width="520" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
							<tr>
								<td width="180" valign="top">
									<span style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif,serif;color:#000000;font-size:14px;text-transform:uppercase">Billing Address</span>
									<div style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px">{{var order.getBillingAddress().format('html')}}</div>
								</td>
								<td width="180" valign="top">
									<span style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;color:#000000;font-size:14px;text-transform:uppercase">Shipping Address</span>
									<div style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px">{{var order.getShippingAddress().format('html')}}</div>
								</td>
								<td width="160" valign="top">
									<span style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;color:#000000;font-size:14px;text-transform:uppercase">PAYMENT DETAILS</span>
									<div style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px">{{var payment_html}}</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				{{layout handle="sales_email_order_items" order=$order}}
				<tr>
					<td width="520" colspan="2" style="padding-top:30px">
						<span style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;color:#000000;font-size:14px;text-transform:uppercase">HAVE QUESTIONS ABOUT YOUR ORDER?</span>
					</td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:10px">
						<span style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px;line-height:1.4em">Visit our <a href="http://www.thelaundress.com/contact/faq/" style="color:#888888">Frequently Asked Questions</a>.</span>
					</td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:10px">
						<span style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px;line-height:1.4em">Email <a href="mailto:sales@thelaundress.com" style="color:#888888">sales@thelaundress.com</a></span>
					</td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:10px">
						<span style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px;line-height:1.4em">Call 212-209-0074 to speak with a Sales Representative.</span>
					</td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:20px">
						<span style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;color:#000000;font-size:14px;text-transform:uppercase">ORDER HISTORY</span>
					</td>
				</tr>
				<tr>
					<td width="520" colspan="2" style="padding-top:5px;padding-bottom:30px">
						<span style="font-family:Arial, Helvetica, Verdana, sans-serif;color:#888888;font-size:12px">To view your order history, go to your <a href="http://www.thelaundress.com/customer/account/" style="color:#888888">Account</a> page and select the My Orders tab.</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto" background="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/footer_tile.gif">
	<tr>
		<td width="100%" height="340" valign="top">
			<table width="520" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto" background="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/footer_card.png">
				<tr>
					<td height="273" valign="top">
						<table width="250" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
							<tr>
								<td width="250" align="center" style="padding-top:170px"><a href="mailto:customerservice@thelaundress.com" style="font-family:Goudy Old Style, Garamond, Big Caslon, Times New Roman, serif;color:#313131;font-size:14px;text-decoration:none"><span style="color:#999999">sales</span>@thelaundress.com</a></td>
							</tr>
							<tr>
								<td width="250" align="center" style="padding-top:3px"><span style="font-family:Gill Sans, Gill Sans MT, Calibri, sans-serif;color:#333333;font-size:14px;text-decoration:none;font-weight:bold">212 209 0074</span></td>
							</tr>
							<tr>
								<td style="padding-top:17px">
									<table width="160" border="0" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto">
										<tr>
											<td width="14" style="padding-right:16px"><a href="http://pinterest.com/thelaundressny/"><img src="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/pinterest.gif" alt="" width="14" height="19" border="0"></a></td>
											<td width="14" style="padding-right:16px"><a href="https://www.facebook.com/thelaundressnyc"><img src="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/facebook.gif" alt="" width="14" height="19" border="0" /></a></td>
											<td width="19" style="padding-right:11px"><a href="https://twitter.com/TheLaundressNY"><img src="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/twitter.gif" alt="" width="19" height="19" border="0" /></a></td>
											<td width="40" style="padding-right:13px"><a href="http://www.youtube.com/user/TheLaundressnyc"><img src="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/youtube.gif" alt="" width="40" height="19" border="0" /></a></td>
											<td width="15" style="padding-right:2px"><a href="http://www.thelaundress.com/rss/"><img src="http://thelaund.nextmp.net/skin/frontend/ultimo/default/images/email/rss.gif" alt="" width="15" height="19" border="0" /></a></td>
										</tr>
									</table>
								</td>
							</tr>
							
						</table>
					</td>
				</tr>
			</table>				
		</td>
	</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
EOT;

$variables = <<< 'EOT'
{"store url=\"\"":"Store Url","var logo_url":"Email Logo Image Url","var logo_alt":"Email Logo Image Alt","htmlescape var=$order.getCustomerName()":"Customer Name","var store.getFrontendName()":"Store Name","store url=\"customer/account/\"":"Customer Account Url","var order.increment_id":"Order Id","var order.getCreatedAtFormated('long')":"Order Created At (datetime)","var order.getBillingAddress().format('html')":"Billing Address","var payment_html":"Payment Details","var order.getShippingAddress().format('html')":"Shipping Address","var order.getShippingDescription()":"Shipping Description","layout handle=\"sales_email_order_items\" order=$order":"Order Items Grid","var order.getEmailCustomerNote()":"Email Order Note"}    
EOT;

$wholesaleWebsite = Mage::getModel('core/website')->load('wholesale','code');

$template = Mage::getModel('core/email_template')->load('Wholesale - New Order', 'template_code')
        ->setTemplateCode('Wholesale - New Order')
        ->setTemplateText($content)
        ->setTemplateStyles(null)
        ->setTemplateType(2)
        ->setTemplateSubject('{{var store.getFrontendName()}}: New Order # {{var order.increment_id}}')
        ->setTemplateSenderName(null)
        ->setTemplateSenderEmail(null)
        ->setAddedAt(null)
        ->setOrigTemplateCode('sales_email_order_template')
        ->setOrigTemplateVariables($variables)
        ->save();

$installer->setConfigData('sales_email/order/template', $template->getTemplateId(), 'websites', $wholesaleWebsite->getId());
$installer->setConfigData('sales_email/order/guest_template', $template->getTemplateId(), 'websites', $wholesaleWebsite->getId());
Mage::app()->getConfig()->reinit();