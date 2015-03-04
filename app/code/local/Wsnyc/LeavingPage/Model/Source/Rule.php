<?php


class Wsnyc_LeavingPage_Model_Source_Rule {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {

        $options = array();
        foreach($this->_getRules() as $rule) {
            $options[] = array('value' => $rule->getId(), 'label' => $rule->getName());
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        $options = array();
        foreach($this->_getRules() as $rule) {
            $options[$rule->getId()] = $rule->getName();
        }

        return $options;
    }

    /**
     * Get all cart rules
     *
     * @return Mage_SalesRule_Model_Resource_Rule_Collection
     */
    protected function _getRules() {
        $collection = Mage::getModel('salesrule/rule')->getCollection();

        return $collection;
    }

}
