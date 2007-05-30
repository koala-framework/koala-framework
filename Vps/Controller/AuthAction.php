<?php
class Vps_Controller_AuthAction extends Vps_Controller_Action
{
    public function preDispatch()
    {
        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = strtolower(str_replace('Controller', '', str_replace('Vps_Controller_Action_', '', get_class($this))));

        if (!($this instanceof Vps_Controller_Action_User) &&
            !$acl->isAllowed($role, $resource))
        {
            if (substr($this->getRequest()->getActionName(), 0, 4) == 'ajax') {
                $ret['success'] = false;
                $ret['login'] = true;
                echo Zend_Json::encode($ret);
                die();
            } else {
                $this->_forward('login', 'user');
            }
        }
    }
    
    protected function _getUserRole()
    {
        $userNamespace = new Zend_Session_Namespace('User');
        return $userNamespace->role;
    }
    
    protected function _getAcl()
    {
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('admin'));
        $acl->add(new Zend_Acl_Resource('pages'));
        $acl->add(new Zend_Acl_Resource('page'));
        $acl->allow('admin', 'pages');
        $acl->allow('admin', 'page');
        return $acl;
    }
      
}
