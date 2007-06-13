<?php
class Vps_Controller_Action extends Zend_Controller_Action
{
    public function preDispatch()
    {
        /*
        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_', '', get_class($this))));
        if ($role != 'guest' &&
            !($this instanceof Vps_Controller_Action_User_Abstract) &&
            !($this instanceof Vps_Controller_Action_Error) &&
            !$acl->isAllowed($role, $resource))
        {
            if ($this->_isAjax()) {
                $ret['success'] = false;
                $ret['login'] = true;
                echo Zend_Json::encode($ret);
                die();
            } else {
                $this->_forward('login', 'user');
            }
        }
        */
    }
    
    public function postDispatch()
    {
        // Menu
        $role = $this->_getUserRole();
        // Nur im Frontend
        if ($role != '' && $this instanceof Vps_Controller_Action_Web) {
            $config['url'] = $this->getRequest()->getPathInfo();
            //$config['_debugMemoryUsage'] = memory_get_usage();
            $renderTo = 'Ext.DomHelper.insertFirst(document.body, \'<div \/>\', true)';
            $this->view->ext('Vps.Menu.Index', $config, $renderTo);
        }
    }
    
    protected function _getUserRole()
    {
        $userNamespace = new Zend_Session_Namespace('User');
        return $userNamespace->role ? $userNamespace->role : 'guest';
    }

    protected function _getAcl()
    {
        return Zend_Registry::get('acl');
    }
    
    protected function _isAjax()
    {
        return substr($this->getRequest()->getActionName(), 0, 4) == 'ajax';
    }
    
}
