<?php
class Vpc_Advanced_DownloadsTree_ViewProjectsController extends Vps_Controller_Action_Auto_Tree
{
    protected $_rootVisible = false;
    protected $_textField = 'text';
    protected $_buttons = array();
    protected $_enableDD = false;
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
        $ret->whereEquals('visible', 1);
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'));
        while($c) {
            foreach (Vpc_Abstract::getSetting($c->componentClass, 'plugins') as $p) {
                if (is_instance_of($p, 'Vps_Component_Plugin_Interface_Login')) {
                    $p = new $p($c->componentId);
                    if (!$p->isLoggedIn()) {
                        return false;
                    }
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }
        return true;
    }
}
