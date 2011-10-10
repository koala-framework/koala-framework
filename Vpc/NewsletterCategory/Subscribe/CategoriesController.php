<?php
class Vpc_NewsletterCategory_Subscribe_CategoriesController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_modelName = 'Vpc_NewsletterCategory_Subscribe_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('limit'=>1, 'ignoreVisible'=>true));
        $nl = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Newsletter_Component', array('subroot'=>$c));

        $values = array();
        $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_CategoriesModel');
        $s = $model->select()
            ->whereEquals('newsletter_component_id', $nl->dbId)
            ->order('pos');
        foreach ($model->getRows($s) as $row) {
            $values[$row->id] = $row->category;
        }
        $select = new Vps_Form_Field_Select();
        $select->setValues($values);
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Bezeichnung'), 200))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column('category'))
            ->setData(new Vps_Data_Table_Parent('Category'));
        $this->_columns->add(new Vps_Grid_Column('category_id', trlVps('Category'), 200))
            ->setEditor($select)
            ->setType('string')
            ->setShowDataIndex('category');
    }
}
