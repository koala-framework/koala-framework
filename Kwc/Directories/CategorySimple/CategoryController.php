<?php
class Kwc_Directories_CategorySimple_CategoryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('add', 'save');

    public function preDispatch()
    {
        $categoryToItemModel = Kwf_Model_Abstract::getInstance(
            Kwc_Abstract::getSetting($this->_getParam('class'), 'categoryToItemModelName')
        );
        $this->_model = $categoryToItemModel->getReferencedModel('Category');

        parent::preDispatch();
    }

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('name', 'Name'));
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->component_id = $this->_getParam('componentId');
        $row->parent_id = $this->_getParam('parent_id') ? $this->_getParam('parent_id') : null;
    }
}
