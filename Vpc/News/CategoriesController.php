<?php
class Vpc_News_CategoriesController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'add'   => true
    );
    protected $_tableName = 'Vpc_News_CategoriesModel';
    protected $_position = 'pos';

    public function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Auto_Grid_Column('category', 'Category', 200))
            ->setEditor(new Vps_Auto_Field_TextField('category', 'Category'));
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 55))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));

    }

}
