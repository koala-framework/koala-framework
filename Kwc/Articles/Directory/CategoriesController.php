<?php
class Kwc_Articles_Directory_CategoriesController extends Kwf_Controller_Action_Auto_Synctree
{
    protected $_model = 'Kwc_Articles_Directory_CategoriesModel';
    protected $_textField = 'name';
    protected $_buttons = array('add', 'edit', 'delete');

    protected function _init()
    {
        parent::_init();
        $this->_editDialog = array(
            'controllerUrl' => Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Category'),
            'width' => 400,
            'height' => 200
        );
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('name'));
        $this->_columns->add(new Kwf_Grid_Column('count_used'));
    }
}
