<?php
class Vps_Controller_Action extends Zend_Controller_Action
{
    protected $_auth = false;
    
    public function preDispatch()
    {
        if ($this->_auth) {
            $acl = $this->_getAcl();
            $role = $this->_getUserRole();
            $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_', '', get_class($this))));
            
            if (!($this instanceof Vps_Controller_Action_User) &&
                !$acl->isAllowed($role, $resource))
            {
                if ($this->_isAjax()) {
                    $ret['success'] = false;
                    $ret['login'] = true;
                    echo Zend_Json::encode($ret);
                    die();
                } else {
                    $this->_forward('login', 'user', '');
                }
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
        $acl = new Vps_Acl();
        
        // Roles
        $acl->addRole(new Vps_Acl_Role('admin'));
        
        // Resources
        $acl->add(new Vps_Acl_Resource('admin', 'Admin'));
            $acl->add(new Vps_Acl_Resource('admin_pages', 'Seitenbaum', '/admin/pages'), 'admin');
            $acl->add(new Zend_Acl_Resource('admin_page'), 'admin');
            $acl->add(new Zend_Acl_Resource('admin_component'), 'admin');
        
        // Berechtigungen
        $acl->allow('admin', 'admin');

        // Seite bearbeiten-Button
        $pageId = $this->getRequest()->getParam('pageId');
        $url = $this->getRequest()->getParam('url');
        if ($pageId != '') {
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageById($pageId);
            $path = $pageCollection->getPath($page);
            $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite betrachten', $path));
            $acl->allow('admin', 'page');
        } else if ($url != '') {
            $pageCollection = Vps_PageCollection_Abstract::getInstance();
            $page = $pageCollection->getPageByPath($url);
            if ($page) {
                $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite bearbeiten', '/admin/page?id=' . $page->getId()));
                $acl->allow('admin', 'page');
            }
        } else {
            $pageId = 0;
        }


        return $acl;
    }
    
    protected function _isAjax()
    {
        return substr($this->getRequest()->getActionName(), 0, 4) == 'ajax';
    }
    
}
