<?php
abstract class Vps_Controller_Action_Auto_Vpc_Grid extends Vps_Controller_Action_Auto_Grid
{
    public function preDispatch()
    {
        if (!isset($this->_model) && !isset($this->_tableName)) {
            $this->setModel(Vpc_Abstract::createModel($this->_getParam('class')));
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

    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig());
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'vps.component';
        $this->view->mainComponentClass = $this->_getParam('class');
        $this->view->baseParams = array('id' => $this->_getParam('componentId'));
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
