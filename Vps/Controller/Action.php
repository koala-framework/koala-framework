<?php
class Vps_Controller_Action extends Zend_Controller_Action
{
    protected $_auth = false;
    
    public function preDispatch()
    {
        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_Component_', '', get_class($this))));
        if (substr($resource, 0, 4) == 'vpc_') {
            $resource = 'component';
        }
        $module = $this->getRequest()->getModuleName();
        if (!$acl->isAllowed($role, $resource)) {
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('jsonLogin', 'login');
            } else {
                $params = array('location' => $this->getRequest()->getPathInfo());
                $this->_forward('index', 'login', null, $params);
            }
        }
    }
    
    protected function _getUserRole()
    {
        $userNamespace = new Zend_Session_Namespace('User');
        return $userNamespace->role ? $userNamespace->role : 'guest';
    }

    protected function _getAcl()
    {
        if (!Zend_Registry::isRegistered('acl')) {
            $acl = new Vps_Acl();
            Zend_Registry::set('acl', $acl);
        }
        return Zend_Registry::get('acl');
    }
    
}
