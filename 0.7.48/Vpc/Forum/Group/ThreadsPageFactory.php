<?php
class Vpc_Forum_Group_ThreadsPageFactory extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vpc_Forum_Thread_Model';
    protected $_componentClass = 'Vpc_Forum_Thread_Component';

    protected function _init()
    {
        parent::_init();
        $this->_componentClass = $this->_getChildComponentClass('thread');
    }

    protected function _getWhere()
    {
        $where = array();
        $where['component_id = ?'] = $this->_component->getDbId();
        return $where;
    }
}
