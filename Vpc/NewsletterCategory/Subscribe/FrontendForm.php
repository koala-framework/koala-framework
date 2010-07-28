<?php
class Vpc_NewsletterCategory_Subscribe_FrontendForm extends Vpc_Newsletter_Subscribe_FrontendForm
{
    protected $_modelName = 'Vpc_NewsletterCategory_Subscribe_Model';
    private $_componentId;

    public function __construct($name, $componentId)
    {
        $this->_componentId = $componentId;
        parent::__construct($name);
    }

    protected function _initFields()
    {
        parent::_initFields();

        $categories = $this->_getCategories();
        if (count($categories) > 1) {
            $this->add(new Vps_Form_Field_MultiCheckbox('ToCategory', 'Category', trlVpsStatic('Categories')))
                ->setValues($categories)
                ->setWidth(255)
                ->setAllowBlank(false);
        }
    }

    protected function _getCategories()
    {
        // Newsletterkategorien werden zum Newsletter gespeichert, welcher
        // Newsletter grade aktuell ist weiß nur die Komponente, deswegen
        // $this->_newsletterComponentId
        $model = Vps_Component_Model::getInstance('Vpc_NewsletterCategory_Subscribe_CategoriesModel');
        $select = $model->select()
            ->whereEquals('component_id', $this->_componentId)
            ->order('pos');
        $categories = array();
        foreach ($model->getRows($select) as $row) {
            $categories[$row->category_id] = $row->name;
        }
        return $categories;
    }

    protected function _afterSave(Vps_Model_Row_Interface $row)
    {
        $categories = $this->_getCategories();
        if (count($categories) == 1) {
            $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_SubscriberToCategory');
            $row = $model->createRow(array(
                'subscriber_id' => $row->id,
                'category_id' => key($categories)
            ));
            $row->save();
        }
    }
}
