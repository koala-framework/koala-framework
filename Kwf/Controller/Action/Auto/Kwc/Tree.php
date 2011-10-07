<?php
abstract class Kwf_Controller_Action_Auto_Kwc_Tree extends Kwf_Controller_Action_Auto_Tree
{
    public function preDispatch()
    {
        if (!isset($this->_table) && !isset($this->_tableName)) {
            if (Kwc_Abstract::hasSetting($this->_getParam('class'), 'tablename')) {
                $tablename = Kwc_Abstract::getSetting($this->_getParam('class'), 'tablename');
                $this->_table = new $tablename(array('componentClass'=>$this->_getParam('class')));
            } else if (Kwc_Abstract::hasSetting($this->_getParam('class'), 'childModel')) {
                $childModelName = Kwc_Abstract::getSetting($this->_getParam('class'), 'childModel');
                $this->_model = new $childModelName(array('componentClass'=>$this->_getParam('class')));
            } else {
                throw new Kwf_Exception('No tablename in Setting defined: ' . $class);
            }
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

    public function indexAction()
    {
        parent::indexAction();
        $this->view->apply(Kwc_Admin::getInstance($this->_getParam('class'))->getExtConfig());
    }
}
