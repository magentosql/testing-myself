<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswersquestion_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('wsnyc_questionsanswers_questionsanswersquestion_form');
        $this->setTitle($this->__('Question Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('wsnyc_questionsanswers');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('question_id' => $this->getRequest()->getParam('question_id'))),
            'method'    => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('wsnyc_questionsanswers')->__('Question Information'),
            'class'     => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('question_id', 'hidden', array(
                'name' => 'question_id',
            ));
        }

        $fieldset->addField('from_backend', 'hidden', array(
            'name' => 'from_backend',
            'values' => '1'
        ));

        $fieldset->addField('published', 'select', array(
            'name'      => 'published',
            'label'     => Mage::helper('checkout')->__('Published'),
            'title'     => Mage::helper('checkout')->__('Published'),
            'required'  => true,
            'values' => array(1=>'Yes',0 => 'No')
        ));

        $fieldset->addField('featured', 'select', array(
            'name'      => 'featured',
            'label'     => Mage::helper('checkout')->__('Featured'),
            'title'     => Mage::helper('checkout')->__('Featured'),
            'required'  => true,
            'values' => array(1=>'Yes',0 => 'No')
        ));

        $fieldset->addField('category_id', 'select', array(
            'name'      => 'category_id',
            'label'     => Mage::helper('checkout')->__('Category'),
            'title'     => Mage::helper('checkout')->__('Category'),
            'required'  => true,
            'values' => Mage::getModel('wsnyc_questionsanswers/source_subcategory')->toOptionArray()
        ));

        $fieldset->addField('asked_name', 'text', array(
            'name'      => 'asked_name',
            'label'     => Mage::helper('checkout')->__('Askers Name'),
            'title'     => Mage::helper('checkout')->__('Askers Name'),
            'required'  => true,
        ));

        $fieldset->addField('asked_email', 'text', array(
            'name'      => 'asked_email',
            'label'     => Mage::helper('checkout')->__('Askers Email'),
            'title'     => Mage::helper('checkout')->__('Askers Email'),
            'required'  => true,
        ));

        $fieldset->addField('question_text', 'textarea', array(
            'name'      => 'question_text',
            'label'     => Mage::helper('checkout')->__('Question Text'),
            'title'     => Mage::helper('checkout')->__('Question Text'),
            'required'  => true,
        ));

        $fieldset->addField('answer_text', 'textarea', array(
            'name'      => 'answer_text',
            'label'     => Mage::helper('checkout')->__('Answer'),
            'title'     => Mage::helper('checkout')->__('Answer'),
            'required'  => true,
        ));

        $fieldset->addField('products', 'textarea', array(
            'name'      => 'products',
            'label'     => Mage::helper('checkout')->__('Associated Product SKUs'),
            'title'     => Mage::helper('checkout')->__('Associated Product SKUs'),
            'required'  => false,
            'note'   =>'One SKU per line'
        ));

        //fill in answer_text that comes from another table
        $answer = Mage::getModel('wsnyc_questionsanswers/answer')->load($model->getId(),'question_id');

        $questionProductsCollection = Mage::getResourceModel('wsnyc_questionsanswers/questionproduct_collection');
        $questionProductsCollection->addFieldToFilter('question_id',array('eq'=>$model->getQuestionId()));

        $questionProducts = array();
        foreach($questionProductsCollection as $questionProduct){
            $questionProducts[] = $questionProduct->getProductSku();
        }

        $data = $model->getData();
        $data['answer_text'] = $answer->getAnswerText();
        $data['products'] = implode(PHP_EOL,$questionProducts);

        if(!$model->getId()){
            $data['from_backend']=1;
        }

        $form->setValues($data);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}