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
        if (Vpc_Abstract::hasSetting($this->_getParam('class'), 'extConfigControllerIndex')) {
            $type = 'extConfigControllerIndex';
        } else {
            //für Abwärtskompatibilität
            $type = 'extConfig';
        }
        $config = Vps_Component_Abstract_ExtConfig_Abstract::getInstance($this->_getParam('class'), $type)
                    ->getConfig(Vps_Component_Abstract_ExtConfig_Abstract::TYPE_DEFAULT);
        if (!$config) {
            throw new Vps_Exception("Not ExtConfig avaliable for this component");
        }
        reset($config);
        $firstConfig = current($config);
        if (count($config) > 1 || (isset($firstConfig['needsComponentPanel']) && $firstConfig['needsComponentPanel'])) {
            $this->view->xtype = 'vps.component';
            $this->view->mainComponentClass = $this->_getParam('class');
            $this->view->baseParams = array('id' => $this->_getParam('componentId'));

            $this->view->componentConfigs = array();
            $this->view->mainEditComponents = array();
            foreach ($config as $k=>$c) {
                $this->view->componentConfigs[$this->_getParam('class').'-'.$k] = $c;
                $this->view->mainEditComponents[] = array(
                    'componentClass' => $this->_getParam('class'),
                    'type' => $k
                );
            }
            $this->view->mainType = $this->view->mainEditComponents[0]['type'];
        } else {
            if (isset($firstConfig['title'])) unset($firstConfig['title']);
            $this->view->assign($firstConfig);
            $this->view->baseParams = array(
                'id' => $this->_getParam('componentId'),
                'componentId' => $this->_getParam('componentId')
            );
        }
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
