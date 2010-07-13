<?php
class Vpc_NewsletterCategory_Subscribe_FrontendForm extends Vpc_Newsletter_Subscribe_FrontendForm
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

        // Newsletterkategorien werden zum Newsletter gespeichert, welcher
        // Newsletter grade aktuell ist weiß nur die Komponente, deswegen
        // $this->_newsletterComponentId
        $model = Vps_Component_Model::getInstance('Vpc_NewsletterCategory_CategoriesModel');
        $select = $model->select()
            ->whereEquals('component_id', $this->_newsletterComponentId)
            ->order('pos');
        $categories = array();
        foreach ($model->getRows($select) as $row) {
            $categories[$row->vps_pool_id] = $row->category;
        }
        if ($categories) {
            $this->add(new Vps_Form_Field_MultiCheckbox('ToPool', 'Pool', trlVps('Categories')))
                ->setValues($categories)
                ->setWidth(255)
                ->setAllowBlank(false);
        }
    }
}
