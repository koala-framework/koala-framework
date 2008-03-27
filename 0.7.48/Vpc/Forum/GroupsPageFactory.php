<?php
class Vpc_Forum_GroupsPageFactory extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vpc_Forum_Group_Model';

    protected function _init()
    {
        parent::_init();
        $this->_componentClass = $this->_getChildComponentClass('group');
    }

    protected function _getWhere()
    {
        $where = array();
        $where['component_id = ?'] = $this->_component->getDbId();
        if (!$this->_showInvisible()) {
            $where[] = 'visible = 1';
        }
        return $where;
    }
}
