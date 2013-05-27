<?php
class Kwc_Directories_CategorySimple_CategoryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_model = 'Kwc_Directories_CategorySimple_CategoriesModel';
    protected $_permissions = array('add', 'save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('name', 'Name'));
    }

    protected function _beforeInsert($row)
    {
        $row->component_id = $this->_getParam('componentId');
        $row->parent_id = $this->_getParam('parent_id') ? $this->_getParam('parent_id') : null;
    }
}
