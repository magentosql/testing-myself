<?php
//  app/code/local/Training/Animal/Model/Animal.php
class Wsnyc_Homepagebanner_Model_Banner extends Mage_Core_Model_Abstract
{
	protected function _construct() {
        // initialize resource model
		$this->_init('wsnyc_homepagebanner/banner');
		
		// this is passed to _setResourceModel,
		// which will also set up our collection
		// model by appending _collection
	}
}