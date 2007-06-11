<?php
class Vps_Controller_Action extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_', '', get_class($this))));
        if (!($this instanceof Vps_Controller_Action_User_Abstract) &&
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
    }
    
    public function postDispatch()
    {
        // Menu
        $role = $this->_getUserRole();
        // Nur im Frontend
        if ($role != '' && $this instanceof Vps_Controller_Action_Web) {
            $files[] = '/Vps/Menu/Index.js';
            $view = new Vps_View_Smarty_Ext($files, 'Vps.Menu.Index', array('url' => $this->getRequest()->getPathInfo()));
            $view->assign('noHead', true);
            $view->assign('renderTo', 'Ext.DomHelper.insertFirst(document.body, \'<div \/>\', true)');
            //$view->assign('_debugMemoryUsage', memory_get_usage());
            $this->getResponse()->appendBody($view->render(''));
        }
    }
    
    protected function _getUserRole()
    {
        $userNamespace = new Zend_Session_Namespace('User');
        return $userNamespace->role;
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
