<?php
class Vpc_NewsletterCategory_Subscribe_CategoriesController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_modelName = 'Vpc_NewsletterCategory_Subscribe_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $values = array();
        $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_CategoriesModel');
        foreach ($model->getRows($model->select()->order('pos')) as $row) {
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
