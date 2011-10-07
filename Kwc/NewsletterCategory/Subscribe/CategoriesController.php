<?php
class Kwc_NewsletterCategory_Subscribe_CategoriesController extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_modelName = 'Kwc_NewsletterCategory_Subscribe_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $values = array();
        $model = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_CategoriesModel');
        foreach ($model->getRows($model->select()->order('pos')) as $row) {
            $values[$row->id] = $row->category;
        }
        $select = new Kwf_Form_Field_Select();
        $select->setValues($values);
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Bezeichnung'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('category'))
            ->setData(new Kwf_Data_Table_Parent('Category'));
        $this->_columns->add(new Kwf_Grid_Column('category_id', trlKwf('Category'), 200))
            ->setEditor($select)
            ->setType('string')
            ->setShowDataIndex('category');
    }
}
