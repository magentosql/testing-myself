<?php

class Wsnyc_CategoryDescriptions_Model_Rule extends Mage_Core_Model_Abstract {

    /**
     * Store rule combine conditions model
     *
     * @var Wsnyc_CategoryDescriptions_Model_Condition_Combine
     */
    protected $_conditions;

    /**
     * Store rule form instance
     *
     * @var Varien_Data_Form
     */
    protected $_form;

    public function _construct() {
        parent::_construct();
        $this->_init('wsnyc_categorydescriptions/rule');
    }

    /**
     * Set rule combine conditions model
     *
     * @param Wsnyc_CategoryDescriptions_Model_Condition_Combine $conditions
     * @return Wsnyc_CategoryDescriptions_Model_Rule
     */
    public function setConditions($conditions) {
        $this->_conditions = $conditions;
        return $this;
    }

    /**
     * Retrieve rule combine conditions model
     *
     * @return Wsnyc_CategoryDescriptions_Model_Condition_Combine
     */
    public function getConditions() {
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }

        // Load rule conditions if it is applicable
        if ($this->hasConditionsSerialized()) {
            $conditions = $this->getConditionsSerialized();
            if (!empty($conditions)) {
                $conditions = unserialize($conditions);
                if (is_array($conditions) && !empty($conditions)) {
                    $this->_conditions->loadArray($conditions);
                }
            }            
            $this->unsConditionsSerialized();
        }

        return $this->_conditions;
    }

    /**
     * Reset rule combine conditions
     *
     * @param null|Wsnyc_CategoryDescriptions_Model_Condition_Combine $conditions
     * @return Wsnyc_CategoryDescriptions_Model_Rule
     */
    protected function _resetConditions($conditions = null) {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix('conditions');
        $this->setConditions($conditions);

        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Wsnyc_CategoryDescriptions_Model_Condition_Combine
     */
    public function getConditionsInstance() {
        return Mage::getModel('wsnyc_categorydescriptions/condition_combine');
    }

    /**
     * Fetch form instance
     * 
     * @return Varien_Data_Form
     */
    public function getForm() {
        if (!$this->_form) {
            $this->_form = new Varien_Data_Form();
        }
        return $this->_form;
    }

    public function loadPost(array $data) {
        $arr = $this->_convertFlatToRecursive($data);        
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        return $this;
    }

    protected function _convertFlatToRecursive(array $data) {
        $arr = array();
        foreach ($data as $key => $value) {
            if (($key === 'conditions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = & $arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
                        }
                        $node = & $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                $this->setData($key, $value);
            }
        }

        return $arr;
    }

    /**
     * Prepare data before saving
     *
     * @return Mage_Rule_Model_Abstract
     */
    protected function _beforeSave() {
        // Serialize conditions
        if ($this->getConditions()) {
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            $this->unsConditions();
        }

        parent::_beforeSave();
        return $this;
    }

}
