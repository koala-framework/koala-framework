<?php
abstract class Vps_Controller_Action_Auto_Vpc_Grid extends Vps_Controller_Action_Auto_Grid
{
    protected $_hasComponentId = true;

    public function preDispatch()
    {
        if (!isset($this->_model) && !isset($this->_tableName) && !isset($this->_modelName)) {
            $this->setModel(Vpc_Abstract::createChildModel($this->_getParam('class')));
        }
        parent::preDispatch();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_hasComponentId) {
            $ret->whereEquals('component_id', $this->_getParam('componentId'));
        }
        return $ret;
    }

    protected function _beforeSave($row)
    {
        if ($this->_hasComponentId) {
            $row->component_id = $this->_getParam('componentId');
        }
    }

    public function indexAction()
    {
        //nicht: parent::indexAction();
        $this->view->xtype = 'vps.component';
        $this->view->mainComponentClass = $this->_getParam('class');
        $this->view->baseParams = array('id' => $this->_getParam('componentId'));

        $this->view->componentConfigs = array();
        $this->view->mainEditComponents = array();
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        if (!$config) {
            throw new Vps_Exception("Not ExtConfig avaliable for this component");
        }
        foreach ($config as $k=>$c) {
            $this->view->componentConfigs[$this->_getParam('class').'-'.$k] = $c;
            $this->view->mainEditComponents[] = array(
                'componentClass' => $this->_getParam('class'),
                'type' => $k
            );
        }
        $this->view->mainType = $this->view->mainEditComponents[0]['type'];
    }

    public function jsonInsertAction()
    {
        //TODO: permissions überprüfen!
        Zend_Registry::get('db')->beginTransaction();
        $row = $this->_model->createRow();
        $this->_beforeInsert($row);
        $this->_beforeSave($row);
        $this->view->id = $row->save();

        Zend_Registry::get('db')->commit();
    }
}
