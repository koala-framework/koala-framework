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

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }

    protected function _beforeSave($row)
    {
        $row->component_id = $this->_getParam('componentId');
    }

    public function jsonIndexAction()
    {
        $conf = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        if ($this->getRequest()->module == 'component_test' && isset($conf['controllerUrl'])) {
            $conf['controllerUrl'] = str_replace('/admin/component/edit/',
                        '/vps/componentedittest/'.Vps_Component_Data_Root::getComponentClass().'/',
                        $conf['controllerUrl']);
        }
        $this->view->vpc($conf);
    }

    public function indexAction()
    {
        //nicht: parent::indexAction();
        $this->view->xtype = 'vps.component';
        $this->view->mainComponentClass = $this->_getParam('class');
        $this->view->baseParams = array('id' => $this->_getParam('componentId'));
        if ($this->getRequest()->module == 'component_test') {
            $this->view->componentEditUrl = '/vps/componentedittest/'.Vps_Component_Data_Root::getComponentClass();
        } else {
            $this->view->componentEditUrl = '/admin/component/edit';
        }
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
