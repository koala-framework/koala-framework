<?php
class Kwc_NewsletterCategory_EditSubscriber_Form extends Kwc_Newsletter_Subscribe_FrontendForm
{
    protected $_modelName = 'Kwc_NewsletterCategory_Subscribe_Model';

    protected function _initFields()
    {
        parent::_initFields();

        $model = Kwf_Component_Model::getInstance('Kwc_NewsletterCategory_CategoriesModel');
        $s = $model->select()
            ->whereEquals('newsletter_component_id', $this->_newsletterComponentId)
            ->order('pos');
        $categories = array();
        foreach ($model->getRows($s) as $row) {
            $categories[$row->id] = $row->category;
        }
        $this->add(new Kwf_Form_Field_MultiCheckbox('ToCategory', 'Category', trlKwf('Categories')))
            ->setValues($categories)
            ->setWidth(255)
            ->setAllowBlank(false);
    }
}
