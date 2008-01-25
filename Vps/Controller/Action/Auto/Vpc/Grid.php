<?php
abstract class Vps_Controller_Action_Auto_Vpc_Grid extends Vps_Controller_Action_Auto_Grid
{
    public function preDispatch()
    {
        if (!isset($this->_table) && !isset($this->_tableName)) {
            $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
            if ($tablename) {
                $this->_table = new $tablename(array('componentClass'=>$this->class));
            } else {
                throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
            }
        }
        parent::preDispatch();
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['component_id = ?'] = $this->componentId;
        return $where;
    }

    protected function _beforeSave($row)
    {
        $row->component_id = $this->componentId;
    }

    public function indexAction()
    {
        $config = Vpc_Admin::getConfig($this->class, $this->componentId);
        $this->view->vpc($config);
    }

    public function jsonInsertAction()
    {
        $row = $this->_table->createRow();
        $this->_beforeInsert($row);
        $this->_beforeSave($row);
        if ($this->_position) {
            $row->pos = 0;
        }
        $this->view->id = $row->save();

        if ($this->_position) {
            $row->numberize($this->_position, null, $this->_getWhere());
        }
    }
}
