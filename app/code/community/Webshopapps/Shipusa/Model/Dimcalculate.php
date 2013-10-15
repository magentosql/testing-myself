<?php
/* UsaShipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Shipusa_Model_Dimcalculate
{

	public function getBoxes($items,$maxPackageWeight=150,$ignoreFreeItems=true,$processingFlat=false) {

    	if (count($items)<1) {
    		return null;
    	}

		$useParent 				= Mage::getStoreConfig('shipping/shipusa/use_parent');
    	$finishedBoxes			= array();
        $finishedFlatBoxes      = array();
    	$dimArr 				= array();
        $flatArr                = array();
    	$shipSeparateArr 		= array();
    	$noDimArr 				= array();
    	$deprecatedDimArr 		= array();

        /**
         * Webshopapps_Shipusa_Model_Shipping_Carrier_Source_Packing
         */
        $packingAlgorithm = Mage::getStoreConfig('shipping/shipusa/packing_algorithm');

    	foreach($items as $item) {

			$weight=0;
			$qty=0;
			$price=0;

			if ($item->isShipSeparately()) {
    			if (!Mage::helper('wsacommon/shipping')->getItemTotals($item, $weight,$qty,$price,false, $ignoreFreeItems)) {
					continue;
				}
	       		$params=$this->getProductParams($item,false,$qty,$weight,$price);
			} else {
				if (!Mage::helper('wsacommon/shipping')->getItemTotals($item, $weight,$qty,$price,$useParent, $ignoreFreeItems)) {
					continue;
				}
	       		$params=$this->getProductParams($item,$useParent,$qty,$weight,$price);
    		}
            if($processingFlat){
                if (!empty($params['flat_boxes'])) {
                    $flatArr[] = $params;
                } else {
                    $flatFound = false;
                }
            } else {
                switch ($packingAlgorithm) {
                    case 'volume_packing':
                        $dimArr[] = $params;
                        break;
                    case 'largest_packing':
                        if ($params['ship_separate']) {
                            $shipSeparateArr[] = $params;
                        } else {
                            $dimArr[] = $params;
                        }
                        break;
                    case 'exact_packing':
                        if (!empty($params['multiple_boxes'])) {
                            $this->populateMultipleBoxes($finishedBoxes,$params);
                        } else if ($params['ship_separate'] || Mage::helper('shipusa')->shipAllSeparate()) {
                            $shipSeparateArr[] = $params;
                        } else if (!empty($params['single_boxes'])) {
                            $dimArr[] = $params;
                        } else if ($params['length']==0 && empty($params['ship_box_id']) ) {  // no dimensions found
                            $noDimArr[] = $params;
                        } else {
                            $deprecatedDimArr[] = $params;
                        } break;
                    default:
                        return null;  // should never hit here
                        break;
                }
            }
        }
        if ($processingFlat) {
            // deal with usps flat rate items - standard items are dealt with in 2nd call.
            if ($flatFound) {
                Mage::getSingleton('shipusa/calculation_singlebox')->getFinishedBoxes($finishedFlatBoxes, $flatArr, true);
            }

            return $finishedFlatBoxes;
        } else {
            switch ($packingAlgorithm) {
                case 'volume_packing':
                    Mage::getSingleton('shipusa/calculation_volumebox')->getFinishedBoxes($finishedBoxes, $dimArr);
                case 'largest_packing':
                    Mage::getSingleton('shipusa/calculation_separatebox')->getFinishedBoxes($finishedBoxes, $shipSeparateArr);
                    Mage::getSingleton('shipusa/calculation_largestbox')->getFinishedBoxes($finishedBoxes, $dimArr);
                    break;
                case 'exact_packing':
                    Mage::getSingleton('shipusa/calculation_separatebox')->getFinishedBoxes($finishedBoxes, $shipSeparateArr);

                    // deal with no dimensional items
                    Mage::getSingleton('shipusa/calculation_stdbox')->getFinishedBoxes($finishedBoxes, $noDimArr);

                    // deal with dimensional items
                    Mage::getSingleton('shipusa/calculation_singlebox')->getFinishedBoxes($finishedBoxes, $dimArr);

                    // process flat items
                    Mage::getSingleton('shipusa/calculation_singlebox')->getFinishedBoxes($finishedFlatBoxes, $flatArr, true);


                    // old logic
                    Mage::getSingleton('shipusa/calculation_oldbox')->getFinishedBoxes($finishedBoxes, $deprecatedDimArr);

                    if (Mage::helper('shipusa')->isDebug()) {
                        Mage::helper('wsalogger/log')->postInfo('usashipping', 'Packages to Send to Carrier', $finishedBoxes);
                    }
                    break;
                default:
                    return null;
                    break;
            }
        }

        if (Mage::helper('shipusa')->isDebug()) {
            Mage::helper('wsalogger/log')->postInfo('usashipping', 'Packages to Send to Carrier', $finishedBoxes);
        }

        return $finishedBoxes;
	}


    /**
     * Used when multiple boxes specified in a listing e.g. bundles
     * @param $finishedBoxes
     * @param array $params
     * @return bool
     */
	private function populateMultipleBoxes(&$finishedBoxes,$params) {

    	for ($j=0;$j<$params['qty'];$j++) {
		    foreach ($params['multiple_boxes'] as $box) {
	    		for ($i=0;$i<$box['num_boxes'];$i++) {
			    	$finishedBoxes[] = array (
			    		'weight' 	=> Mage::helper('shipusa')->getWeightCeil($box['weight']), // doesnt have packing weight set
		    			'price'		=> Mage::helper('shipusa')->toTwoDecimals($box['declared_value']),
			    		'length' 	=> Mage::helper('shipusa')->toTwoDecimals($box['length']),
			    		'width' 	=> Mage::helper('shipusa')->toTwoDecimals($box['width']),
			    		'height'	=> Mage::helper('shipusa')->toTwoDecimals($box['height']),
			    	    'qty'		=> $box['quantity'],
						'handling_fee'  	=> 0,
			    	);
	    		}
		    }
    	}

    	if (Mage::helper('shipusa')->isDebug()) {
    		Mage::helper('wsalogger/log')->postDebug('usashipping','populateMultipleBoxes',$finishedBoxes);
    	}

    	return true;
    }

	/*****************************************************************************
	 * NEW METHODS
	 *
	 * //TODO Ship All Dimensional logic
	 */
	private function getProductParams($item,$useParent,$qty,$weight,$price)
    {
    	if ($item->getParentItem()!=null &&
			$useParent && !$item->getParentItem()->isShipSeparately()) {
			// must be a bundle, not shipping separately
		    $product = $item->getParentItem()->getProduct();
		} else if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && !$useParent ) {
			if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
		            $product=$child->getProduct();
				    break;
                }
			}
		} else {
			$product = $item->getProduct();
		}

		$params=array();

        $params['id']					= $product->getId();
		$params['multiple_boxes'] 		= $this->getShipBoxes($params['id']);
        $params['single_boxes'] 		= $this->getSingleBoxes($params['id']);
        $params['flat_boxes']           = $this->getFlatBoxes($params['id']);
        $params['weight'] 				= $weight;
        $params['orig_weight'] 		    = $weight;
        $params['qty'] 					= $qty;
        $params['orig_qty'] 			= $qty;
		$params['price'] 				= $price;
		$params['length'] 				= Mage::helper('shipusa')->toTwoDecimals($product->getData('ship_length'));
        $params['width'] 				= Mage::helper('shipusa')->toTwoDecimals($product->getData('ship_width'));
	    $params['height'] 				= Mage::helper('shipusa')->toTwoDecimals($product->getData('ship_height'));
	    $params['shared_max_qty']		= $product->getData('ship_shared_max_qty'); //TODO Was length, check logic
	    $params['alt_box_id'] 			= $product->getData('ship_alternate_box');  // deprecated
        $params['ship_algorithm'] 		= $product->getData('ship_algorithm');
        $params['ship_separate'] 		= $product->getData('ship_separately');
        $params['num_boxes'] 			= $product->getData('ship_num_boxes');
        $params['split_product'] 		= $product->getData('split_product') == 1 ? true : false;


	   	if (Mage::helper('shipusa')->isHandlingProdInstalled()) {
    		$params['shipping_addon'] 	= $product->getData('handling_addon');
			$params['shipping_percent'] =  $product->getData('handling_is_percent');
			$params['shipping_price'] 	=  $product->getData('handling_price');
        } else {
        	$params['shipping_addon']	= 0;
			$params['shipping_percent'] =  0;
			$params['shipping_price'] 	=  0;
        }
 	    $params['ship_box_id'] = $product->getData('ship_box');
	  	if ($params['ship_box_id']=='' && (empty($params['length']) || $params['length']<=0 ||
	  			empty($params['width']) || $params['width']<=0 || empty($params['height']) || $params['height']<=0 )	) {
              if (empty($params['single_boxes']) && Mage::getStoreConfig('shipping/shipusa/default_box_size')!='') {
                // push in single box logic
                  $params['single_boxes'] = $this->getDefaultBox();
              }
	  	}

        if (empty($params['length']) || empty($params['width']) || empty($params['height'])) {
        	Webshopapps_Shipusa_Model_Calculation_Boxsettings::populateBoxSettings($params,true);
        } else {
	        Webshopapps_Shipusa_Model_Calculation_Boxsettings::populateBoxSettings($params,false);
        }

		if ($item->isShipSeparately() ||
			($item->getParentItem()!=null && $useParent && $item->getParentItem()->isShipSeparately())) {
			$params['ship_separate'] = true;
		}
        if (!is_numeric($params['num_boxes']) || $params['num_boxes']<1) {
	    	$params['num_boxes']=1;
		}

        // TODO: Note to customer that ship dimensional false no longer supported
        //TODO Ship All Dimensional

        if (Mage::helper('shipusa')->isDebug()) {
       		Mage::helper('wsalogger/log')->postDebug('usashipping','product id:'.$params['id'],$params);
        }

        return $params;
	}

    private function getDefaultBox() {
        $defaultBoxId=Mage::getStoreConfig('shipping/shipusa/default_box_size');

        $boxes = array();
        $key=0;

        $boxes[$key]['box_id'] 	            = $defaultBoxId;
        $boxes[$key]['max_qty'] 	        = -1;
        $boxes[$key]['box_volume'] 	        = 10;
        $boxes[$key]['item_volume'] 	    = -1;
        $boxes[$key]['max_box'] 	        = -1;
        $boxes[$key]['min_qty'] 	        = 0;
        $boxes[$key]['max_shipbox_weight'] 	= -1;
        $boxes[$key]['max_shipbox_qty'] 	= -1;
        $boxes[$key]['packing_weight'] 		= 0;
        $boxDetails = Mage::getModel('boxmenu/boxmenu')->load($defaultBoxId);
        $boxes[$key]['length']=$boxDetails->getLength();
        $boxes[$key]['width']=$boxDetails->getWidth();
        $boxes[$key]['height']=$boxDetails->getHeight();
        $boxes[$key]['box_volume']=$boxDetails->getVolume();
        $boxes[$key]['item_volume']= $boxes[$key]['max_box'] == -1  ? -1 : $boxes[$key]['box_volume']/$boxes[$key]['max_box'];
        $boxes[$key]['perc_qty_per_item']=$boxDetails->getMultiplier() > 0 ? (1/$boxDetails->getMultiplier())*100 : -1;

        if ($boxDetails->getMaxWeight() > 0 ) {
            $boxes[$key]['max_shipbox_weight']=$boxDetails->getMaxWeight();
        }
        if ($boxDetails->getMultiplier() > 0 ) {
            $boxes[$key]['max_shipbox_qty']=$boxDetails->getMultiplier();
        }
        $packingWeight = $boxDetails->getPackingWeight();
        $boxes[$key]['packing_weight'] = $packingWeight > 0 ? $packingWeight : 0;

        return $boxes;
    }


	/****
	 * Retrieving data
	 */
 	public function getShipBoxes($productId)
    {
        $shipBoxes = Mage::getModel('shipusa/shipboxes')->getCollection()
            ->addProductFilter($productId);
        $data = $shipBoxes->getData();
        if (!is_array($data) || count($data)<1) {
        	$data='';
        }
        return $data;
    }


 	public function getSingleBoxes($productId)
    {
        $singleBoxes = Mage::getModel('shipusa/singleboxes')->getCollection()
            ->addProductFilter($productId);
        $boxes = $singleBoxes->getData();
        if (!is_array($boxes) || count($boxes)<1) {
        	$boxes=array();
        }
        foreach ($boxes as $key=>$box) {
        	$boxes[$key]['max_shipbox_weight'] 	= -1;
            $boxes[$key]['max_shipbox_qty'] 	= -1;
            $boxes[$key]['perc_qty_per_item'] 	= -1;
            $boxes[$key]['packing_weight'] 		= 0;
        	if ($box['box_id']!=0) {
        		$boxDetails = Mage::getModel('boxmenu/boxmenu')->load($box['box_id']);
        		$boxes[$key]['length']=$boxDetails->getLength();
        		$boxes[$key]['width']=$boxDetails->getWidth();
        		$boxes[$key]['height']=$boxDetails->getHeight();
        		$boxes[$key]['box_volume']=$boxDetails->getVolume();
        		$boxes[$key]['item_volume']= $boxes[$key]['max_box'] == -1  ? -1 : $boxes[$key]['box_volume']/$boxes[$key]['max_box'];

        		if ($boxDetails->getMaxWeight() > 0 ) {
        			$boxes[$key]['max_shipbox_weight']=$boxDetails->getMaxWeight();
        		}
        		if ($boxDetails->getMultiplier() > 0 ) {
        			$boxes[$key]['max_shipbox_qty']=$boxDetails->getMultiplier();
                    $boxes[$key]['perc_qty_per_item']=$boxDetails->getMultiplier() > 0 ? (1/$boxDetails->getMultiplier())*100 : -1;
                }
                $packingWeight = $boxDetails->getPackingWeight();
                $boxes[$key]['packing_weight'] = $packingWeight > 0 ? $packingWeight : 0;
        	} else {
                $boxes[$key]['perc_qty_per_item']=$boxes[$key]['max_box'] > 0 ? (1/$boxes[$key]['max_box'])*100 : -1;
            }
        }
        return $boxes;
    }

    public function getFlatBoxes($productId)
    {
        $singleBoxes = Mage::getModel('shipusa/flatboxes')->getCollection()
            ->addProductFilter($productId);
        $boxes = $singleBoxes->getData();
        if (!is_array($boxes) || count($boxes)<1) {
            $boxes=array();
        }
        foreach ($boxes as $key=>$box) {
            $boxes[$key]['max_shipbox_weight'] 	= -1;
            $boxes[$key]['max_shipbox_qty'] 	= -1;
            $boxes[$key]['packing_weight'] 		= 0;
            $boxes[$key]['perc_qty_per_item'] 	= -1;
            $boxes[$key]['box_id']              = $box['box_id'];
            if ($box['box_id'] > 3) {
                $boxDetails = Mage::getModel('boxmenu/boxmenu')->load($box['box_id']);
                $boxes[$key]['box_type'] = $boxDetails->getBoxType();
                $boxes[$key]['length']=-1;
                $boxes[$key]['width']=-1;
                $boxes[$key]['height']=-1;
                $boxes[$key]['box_volume']=-1;
                $boxes[$key]['item_volume']= $boxes[$key]['max_box'] == -1  ? -1 : $boxes[$key]['box_volume']/$boxes[$key]['max_box'];

                if ($boxDetails->getMaxWeight() > 0 ) {
                    $boxes[$key]['max_shipbox_weight']=$boxDetails->getMaxWeight();
                }
                if ($boxDetails->getMultiplier() > 0 ) {
                    $boxes[$key]['max_shipbox_qty']=$boxDetails->getMultiplier();
                    $boxes[$key]['perc_qty_per_item']=$boxDetails->getMultiplier() > 0 ? (1/$boxDetails->getMultiplier())*100 : -1;
                }
                $packingWeight = $boxDetails->getPackingWeight();
                $boxes[$key]['packing_weight'] = $packingWeight > 0 ? $packingWeight : 0;
            } else {
                $boxes[$key]['perc_qty_per_item']=$boxes[$key]['max_box'] > 0 ? (1/$boxes[$key]['max_box'])*100 : -1;
            }
        }
        return $boxes;
    }
}
