<?php
class Kwf_Component_Generator_Plugin_Tags_Controller extends Kwf_Controller_Action_Auto_AssignGrid
{
    protected $_assignFromReference = 'Tag';

    protected function _createAssignRow($id)
    {
        $row = $this->_getModel()->createRow();
        $row->tag_id = $id;
        $row->component_id = $this->_getParam('componentId');
        return $row;
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->setModel(Kwc_Abstract::createChildModel($this->_getParam('class')));
        $this->_columns->add(new Kwf_Grid_Column('tag_text', trlKwf('Tag')));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
