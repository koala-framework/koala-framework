<?php
class Vpc_Advanced_DownloadsTree_ProjectsController extends Vps_Controller_Action_Auto_Tree
{
    protected $_rootVisible = true;
    protected $_textField = 'text';
    protected $_buttons = array('add', 'edit', 'delete');
    protected $_enableDD = true;
    protected $_hasPosition = true;

    public function preDispatch()
    {
        $this->_modelName = Vpc_Abstract::getSetting($this->_getParam('class'), 'projectsModel');
        parent::preDispatch();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
