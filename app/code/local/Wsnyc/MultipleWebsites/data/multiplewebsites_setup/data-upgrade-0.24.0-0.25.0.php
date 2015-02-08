<?php

$installer = $this;

Mage::getModel('checkout/agreement')->load('THE LAUNDRESS INTERNATIONAL TERMS & CONDITIONS', 'name')->delete();

$pageContent = <<< EOT
<h2><span style="text-decoration: underline;">THE LAUNDRESS DOMESTIC TERMS & CONDITIONS</span></h2>
<p>ACCEPTANCE: This sales acknowledgement, and these terms and conditions, shall be deemed accepted by Buyer upon ordering and paying for the merchandise set forth herein.  All purchase orders of Buyer are subject to the approval of Seller.  Buyer shall resell the merchandise only to consumers and only from those locations expressly authorized by Seller. </p>
<p>DELIVERY TERMS: Unless otherwise specified in this agreement, all shipments shall be F.O.B. Seller’s warehouse with transportation, freight, handling and related charges to be added to the total amount charged to Buyer by Seller at the time of shipment.  Seller will select the carrier for Buyer’s merchandise but Seller will not thereby assume liability in connection with the shipment nor shall the carrier be deemed the agent of Seller. Please allow 1-2 weeks for shipping with the exception of backorders.</p>
<p>TITLE TO AND RISK OF LOSS: Title to, and risk of loss for, the merchandise shall pass to Buyer on delivery, which shall be deemed made when the merchandise is ready for pickup by the carrier.</p>
<p>PAYMENT TERMS: Unless otherwise specified in this agreement, payment must be made by credit card or wire transfer prior to delivery of the merchandise.</p>
<p>MINIMUM ORDER: There is a minimum order amount for the initial merchandise of $350 and on reorders. Order requests for less than the minimum amount shall incur a 20% shipping charge. Requests for partial cases will incur a charge of $2 per broken case.</p>
<p>RETAIL PRICES: All of The Laundress retail prices are MAP pricing(maximum advertised prices)</p>
<p>MODIFICATIONS: All order modifications must be submitted within 24 hours of confirmed PO receipt from The Laundress.</p>
<p>DEFECTS: Each delivery shall be conclusively deemed in accordance with this agreement unless within 24 hours of receiving delivery of the merchandise Buyer notifies Seller in writing of the rejection, specifies the reasons for rejection and Buyer gives Seller an opportunity to inspect the rejected merchandise.  The exclusive remedies of the Buyer against the Seller shall be replacement by Seller of the defective or incorrect merchandise.  Buyer shall not be entitled to any credit for damaged or otherwise returned merchandise.</p>
<p>FORCE MAJEURE: Buyer should expect at least 5 to 15 business days until shipment of the ordered merchandise is made.  Seller shall not be liable for damages or penalties for any delay or failure in performance because of any act of God, act of government, act of Buyer, war, civil disturbance, riot, flood, fire, epidemic, quarantine, strike or other act of labor, sabotage, accident, machinery breakdown, electrical failure, severe weather, vendor delay or other cause beyond Seller’s reasonable control.  Such delay or failure shall not give Buyer the right to terminate this agreement and Seller shall have the right, at its option, to extend its delivery time or to cancel the agreement.</p>
<p>WARRANTIES: The fair average quality of merchandise sold by sample and/or description shall approximate the quality of the sample or description.  EXCEPT AS EXPRESSLY SET FORTH IN THIS AGREEMENT, SELLER MAKES NO WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, WHETHER OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR OTHERWISE.  Seller shall not be liable for incidental and/or consequential damages stemming from the sale or use of the merchandise and Seller’s maximum liability for damages shall in no event exceed the purchase price of the merchandise concerning which a claim is made.  </p>
<p>INTELLECTUAL PROPERTY: Seller is the exclusive owner of the trademarks THE LAUNDRESS and the Lady Design associated therewith and other trademarks, service marks, domain names, copyrights, patents, trade names and logos of Seller and the good will associated therewith.</p>
<p>MODIFICATION: This agreement constitutes the entire agreement between Buyer and Seller with all prior representations and understandings being merged herein and the terms of this agreement shall supersede any purchase order of the Purchaser. </p>
<p>ARBITRATION: Any controversy arising out of this agreement shall be determined by arbitration which shall be held in the City of New York in accordance with the rules then in effect in the American Arbitration Association.  Such arbitration shall be binding and conclusive upon all parties.  </p>
<p>APPLICABLE LAW: This agreement shall be deemed to have been entered into and shall be construed and interpreted in accordance with the laws of the State of New York.</p>

<h2 style="text-decoration: underline;">THE LAUNDRESS INTERNATIONAL TERMS & CONDITIONS</h3>
<p>ACCEPTANCE: This sales acknowledgement, and these terms and conditions, shall be deemed accepted by Buyer upon ordering and paying for the merchandise set forth herein.  All purchase orders of Buyer are subject to the approval of Seller.  Buyer shall resell the merchandise only to consumers and only from those locations expressly authorized by Seller. </p>
<p>DELIVERY TERMS: Unless otherwise specified in this agreement, all shipments shall be F.O.B. Seller’s warehouse with transportation, freight, handling and related charges to be added to the total amount charged to Buyer by Seller at the time of shipment. Buyer will pay carrier for all duties, taxes, and import fees. Seller will select the carrier for Buyer’s merchandise but Seller will not thereby assume liability in connection with the shipment nor shall the carrier be deemed the agent of Seller. Please allow 1-2 weeks for shipping with the exception of backorders.</p>
<p>TITLE TO AND RISK OF LOSS: Title to, and risk of loss for, the merchandise shall pass to Buyer on delivery, which shall be deemed made when the merchandise is ready for pickup by the carrier.</p>
<p>PAYMENT TERMS: Unless otherwise specified in this agreement, payment must be made by credit card or wire transfer prior to delivery of the merchandise.</p>
<p>MINIMUM ORDER: Outside of the Unites States Buyer will receive a 20% discount from US wholesale prices (some exceptions apply). There is a minimum order amount for the initial merchandise of $1,000 and on reorders. For order requests for less than the minimum amount, the 20% discount will not apply.</p>
<p>RETAIL PRICES: All of The Laundress retail prices are MAP pricing (maximum advertised prices)</p>
<p>MODIFICATIONS: All order modifications must be submitted within 24 hours of confirmed PO receipt from The Laundress.</p>
<p>DEFECTS: Each delivery shall be conclusively deemed in accordance with this agreement unless within 24 hours of receiving delivery of the merchandise Buyer notifies Seller in writing of the rejection, specifies the reasons for rejection and Buyer gives Seller an opportunity to inspect the rejected merchandise.  The exclusive remedies of the Buyer against the Seller shall be replacement by Seller of the defective or incorrect merchandise.  Buyer shall not be entitled to any credit for damaged or otherwise returned merchandise.</p>
<p>FORCE MAJEURE: Buyer should expect at least 5 to 15 business days until shipment of the ordered merchandise is made.  Seller shall not be liable for damages or penalties for any delay or failure in performance because of any act of God, act of government, act of Buyer, war, civil disturbance, riot, flood, fire, epidemic, quarantine, strike or other act of labor, sabotage, accident, machinery breakdown, electrical failure, severe weather, vendor delay or other cause beyond Seller’s reasonable control.  Such delay or failure shall not give Buyer the right to terminate this agreement and Seller shall have the right, at its option, to extend its delivery time or to cancel the agreement.</p>
<p>WARRANTIES: The fair average quality of merchandise sold by sample and/or description shall approximate the quality of the sample or description.  EXCEPT AS EXPRESSLY SET FORTH IN THIS AGREEMENT, SELLER MAKES NO WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, WHETHER OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR OTHERWISE.  Seller shall not be liable for incidental and/or consequential damages stemming from the sale or use of the merchandise and Seller’s maximum liability for damages shall in no event exceed the purchase price of the merchandise concerning which a claim is made.  </p>
<p>INTELLECTUAL PROPERTY: Seller is the exclusive owner of the trademarks THE LAUNDRESS and the Lady Design associated therewith and other trademarks, service marks, domain names, copyrights, patents, trade names and logos of Seller and the good will associated therewith.</p>
<p>MODIFICATION: This agreement constitutes the entire agreement between Buyer and Seller with all prior representations and understandings being merged herein and the terms of this agreement shall supersede any purchase order of the Purchaser. </p>
<p>ARBITRATION: Any controversy arising out of this agreement shall be determined by arbitration which shall be held in the City of New York in accordance with the rules then in effect in the American Arbitration Association.  Such arbitration shall be binding and conclusive upon all parties.  </p>
<p>APPLICABLE LAW: This agreement shall be deemed to have been entered into and shall be construed and interpreted in accordance with the laws of the State of New York.</p>

EOT;

$pageXmlUpdate = <<< EOT
<reference name="left">
        <block type="core/template" name="contact_sidebar" template="contact/sidebar.phtml" ></block> 
</reference>
EOT;

$retailStore = Mage::getModel('core/store')->load('default','code');
$wholesaleStore = Mage::getModel('core/store')->load('wholesaledefault','code');

$cmsPage = Mage::getModel('cms/page')->getCollection()
        ->addFieldToFilter('identifier', array('eq' => 'customer-service/terms'))
        ->addStoreFilter($retailStore)
        ->load()
        ->getFirstItem();
        
$cmsPage->setStores(array($retailStore->getId()))
        ->save();

Mage::getModel('cms/page')
        ->setIdentifier('customer-service/terms')
        ->setTitle('Terms & Conditions')
        ->setRootTemplate('two_columns_left')
        ->setIsActive('1')
        ->setStores(array($wholesaleStore->getId()))
        ->setContent($pageContent)
        ->setContentHeading('Terms & Conditions')
        ->setLayoutUpdateXml($pageXmlUpdate)
        ->save();