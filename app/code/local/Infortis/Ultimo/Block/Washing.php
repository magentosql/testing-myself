<?php
/**
 * 
 */

class Infortis_Ultimo_Block_Washing extends Mage_Core_Block_Template
{

     public function getCategoryChildren($category_id)
     {
          $catalog = Mage::getModel('catalog/category');
          $children_ids = explode(',', $catalog->load($category_id)->getChildren());
          
          $children = array();
          foreach($children_ids as $child):
	      $children[$child] = $catalog->load($child);
	  endforeach;

 	  return $children_ids;
     }

     public function getCatelogItems($category_id)
     {
         $category = Mage::getModel('catalog/category')->load($category_id);
         $productlist = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);

         $collection = $category->getProductCollection();
         $collection->addAttributeToSelect('*');
     
         $productlist = $collection->getItems();
         return $productlist;
     }      


     public function getClothingCategories()
     {
         return $this->getCategoryChildren(29);
     }

     public function getHouseholdCategories()
     {
         return $this->getCategoryChildren(30);
     }

     public function getClothingProducts()
     {
         return $this->getCatelogItems(29);
     }

     public function getHouseholdProducts()
     {
         return $this->getCatelogItems(30);
     }



     public function getNextContainer($class)
     {
         if (empty($class)):
             return "left";
         elseif ($class == "left"):
             return "mid";
	 endif;
         
         return "right";
     }




}
