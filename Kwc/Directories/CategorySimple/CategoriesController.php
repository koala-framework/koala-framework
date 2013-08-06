<?php
class Kwc_Directories_CategorySimple_CategoriesController extends Kwf_Controller_Action_Auto_Synctree
{
    protected $_model = 'Kwc_Directories_CategorySimple_CategoriesModel';
    protected $_textField = 'name';
    protected $_editDialog = array(
        'width' => 400,
        'height' => 200
    );
    protected $_buttons = array('add', 'edit', 'delete');

    protected function _init() {
        $this->_editDialog['controllerUrl'] =
            Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Category');
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('name'));
        $this->_columns->add(new Kwf_Grid_Column('count_used'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
