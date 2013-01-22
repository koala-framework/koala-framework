<?php
class Kwc_Articles_Directory_CategoryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_model = 'Kwc_Articles_Directory_CategoriesModel';
    protected $_permissions = array('add');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('name', 'Name'));
    }

    protected function _beforeInsert($row)
    {
        $row->parent_id = (($this->_getParam('parent_id')) ? $this->_getParam('parent_id') : NULL);
    }
}
