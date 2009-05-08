<?php
class Vpc_Advanced_DownloadsTree_ViewProjectsController extends Vps_Controller_Action_Auto_Tree
{
    protected $_modelName = 'Vpc_Advanced_DownloadsTree_Projects';
    protected $_rootVisible = false;
    protected $_textField = 'text';
    protected $_buttons = array();
    protected $_enableDD = false;
    protected $_hasPosition = true;

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['component_id = ?'] = $this->_getParam('component_id');
        $ret[] = 'visible = 1';
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }

}
