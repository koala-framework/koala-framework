<?php
class Vps_Component_Generator_Plugin_Tags_Controller extends Vps_Controller_Action_Auto_AssignGrid
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
        $this->setModel(Vpc_Abstract::createChildModel($this->_getParam('class')));
        $this->_columns->add(new Vps_Grid_Column('tag_text', trlVps('Tag')));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
