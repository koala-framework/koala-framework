<?php
class Vpc_NewsletterCategory_EditSubscriber_Form extends Vpc_Newsletter_Subscribe_FrontendForm
{
    protected $_modelName = 'Vpc_NewsletterCategory_Subscribe_Model';
    private $_newsletterComponentId;

    public function __construct($name, $newsletterComponentId)
    {
        $this->_newsletterComponentId = $newsletterComponentId;
        parent::__construct($name);
    }

    protected function _initFields()
    {
        parent::_initFields();

        $model = Vps_Component_Model::getInstance('Vpc_NewsletterCategory_CategoriesModel');
        $s = $model->select()
            ->whereEquals('newsletter_component_id', $this->_newsletterComponentId)
            ->order('pos');
        $categories = array();
        foreach ($model->getRows($s) as $row) {
            $categories[$row->id] = $row->category;
        }
        $this->add(new Vps_Form_Field_MultiCheckbox('ToCategory', 'Category', trlVps('Categories')))
            ->setValues($categories)
            ->setWidth(255)
            ->setAllowBlank(false);
    }
}
