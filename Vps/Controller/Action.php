<?php
class Vps_Controller_Action extends Zend_Controller_Action
{
    protected $_auth = false;
    
    public function preDispatch()
    {
        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_', '', get_class($this))));
        if (($this->_auth || substr($resource, 0, 5) == 'admin') && 
            $resource != 'admin_user' &&
            !$acl->isAllowed($role, $resource))
        {
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('jsonLogin', 'user');
            } else {
                $this->_forward('login', 'user', null, array('location' => $this->getRequest()->getPathInfo()));
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
