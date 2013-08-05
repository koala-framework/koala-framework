<?php
class Kwc_NewsletterCategory_Subscribe_CategoriesController extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_modelName = 'Kwc_NewsletterCategory_Subscribe_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('limit'=>1, 'ignoreVisible'=>true));
        $nl = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Newsletter_Component', array('subroot'=>$c));

        $values = array();
        $model = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_CategoriesModel');
        $s = $model->select()
            ->whereEquals('newsletter_component_id', $nl->dbId)
            ->order('pos');
        foreach ($model->getRows($s) as $row) {
            $values[$row->id] = $row->category;
        }
        $select = new Kwf_Form_Field_Select();
        $select->setValues($values)
            ->setAllowBlank(false);
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Label'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('category'))
            ->setData(new Kwf_Data_Table_Parent('Category'));
        $this->_columns->add(new Kwf_Grid_Column('category_id', trlKwf('Category'), 200))
            ->setEditor($select)
            ->setType('string')
            ->setShowDataIndex('category');
    }
}
