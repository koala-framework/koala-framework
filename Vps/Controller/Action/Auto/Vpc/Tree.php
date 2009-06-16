<?php
abstract class Vps_Controller_Action_Auto_Vpc_Tree extends Vps_Controller_Action_Auto_Tree
{
    public function preDispatch()
    {
        if (!isset($this->_table) && !isset($this->_tableName)) {
            $tablename = Vpc_Abstract::getSetting($this->_getParam('class'), 'tablename');
            if ($tablename) {
                $this->_table = new $tablename(array('componentClass'=>$this->_getParam('class')));
            } else {
                throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
            }
        }
        parent::preDispatch();
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['component_id = ?'] = $this->_getParam('componentId');
        return $where;
    }

    protected function _beforeSave($row)
    {
        $row->component_id = $this->_getParam('componentId');
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->apply(Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig());
    }
}
