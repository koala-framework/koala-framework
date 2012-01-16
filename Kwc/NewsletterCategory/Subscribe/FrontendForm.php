<?php
class Kwc_NewsletterCategory_Subscribe_FrontendForm extends Kwc_Newsletter_Subscribe_FrontendForm
{
    protected $_modelName = 'Kwc_NewsletterCategory_Subscribe_Model';
    private $_subscribeComponentId;

    public function __construct($name, $subscribeComponentId)
    {
        $this->_subscribeComponentId = $subscribeComponentId;
        parent::__construct($name);
    }

    protected function _initFields()
    {
        parent::_initFields();

        $categories = $this->_getCategories();
        if (count($categories) > 1) {
            $this->add(new Kwf_Form_Field_MultiCheckbox('ToCategory', 'Category', trlKwfStatic('Categories')))
                ->setValues($categories)
                ->setWidth(255)
                ->setAllowBlank(false);
        }
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $this->addCategoryIfOnlyOne($row);
    }

    public function getCategories()
    {
        return $this->_getCategories();
    }

    protected function _getCategories()
    {
        // Newsletterkategorien werden zum Newsletter gespeichert, welcher
        // Newsletter grade aktuell ist weiß nur die Komponente, deswegen
        // $this->_subscribeComponentId
        $model = Kwf_Component_Model::getInstance('Kwc_NewsletterCategory_Subscribe_CategoriesModel');
        $select = $model->select()
            ->whereEquals('component_id', $this->_subscribeComponentId)
            ->order('pos');
        $categories = array();
        foreach ($model->getRows($select) as $row) {
            $categories[$row->category_id] = $row->name;
        }
        return $categories;
    }

    public function addCategoryIfOnlyOne(Kwf_Model_Row_Interface $row)
    {
        $categories = $this->_getCategories();
        if (count($categories) == 1) {
            $model = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_Subscribe_SubscriberToCategory');
            $row = $model->createRow(array(
                'subscriber_id' => $row->id,
                'category_id' => key($categories)
            ));
            $row->save();
        }
    }
}
