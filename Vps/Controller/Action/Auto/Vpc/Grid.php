<?php
abstract class Vps_Controller_Action_Auto_Vpc_Grid extends Vps_Controller_Action_Auto_Grid
{
    public function preDispatch()
    {
        if (!isset($this->_model) && !isset($this->_tableName)) {
            $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
            if ($tablename) {
                $this->setTable(new $tablename(array('componentClass'=>$this->class)));
            } else {
                throw new Vpc_Exception('No tablename in Setting defined for ' . $this->class);
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

    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->class)->getExtConfig());
    }

    public function jsonInsertAction()
    {
        //TODO: permissions überprüfen!
        Zend_Registry::get('db')->beginTransaction();
        $row = $this->_model->createRow();
        $this->_beforeInsert($row);
        $this->_beforeSave($row);
        if ($this->_position) {
            $row->pos = 0;
        }
        $this->view->id = $row->save();

        Zend_Registry::get('db')->commit();
    }
}
