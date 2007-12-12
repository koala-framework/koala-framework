<?php
abstract class Vps_Controller_Action extends Zend_Controller_Action
{
    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    public function preDispatch()
    {
        if (!$this instanceof Vps_Controller_Action_Error
                && $this->_getParam('application_version')
                && $this->getHelper('ViewRenderer')->isJson()) {
            $version = Zend_Registry::get('config')->application->version;
            if ($version != $this->_getParam('application_version')) {
                $this->_forward('jsonWrongVersion', 'error',
                                    'vps_controller_action_error');
            }
        }

        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = $this->_getResourceName();

        if (!$acl->isAllowed($role, $resource, 'view')) {
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('jsonLogin', 'login',
                                    'vps_controller_action_user');
            } else {
                $params = array('location' => $this->getRequest()->getPathInfo());
                $this->_forward('index', 'login',
                                    'vps_controller_action_user', $params);
            }
        }
    }

    protected function _getResourceName()
    {
//         d(get_class($this));
        $resource = strtolower(str_replace(array('Vps_Controller_Action_Component_',
                                                 'Vps_Controller_Action_',
                                                 'Controller'),
                                        '', get_class($this)));
        if (substr(get_class($this), 0, 4) == 'Vps_') {
            $resource = 'vps_'.$resource;
        }
        if (substr($resource, 0, 4) == 'vpc_') {
            $resource = 'vps_component';
        }
        return $resource;
    }

    protected function _getUserRole()
    {
        return $this->_getAuthData() ? $this->_getAuthData()->role : 'guest';
    }

    protected function _getAuthData()
    {
        return Zend_Auth::getInstance()->getStorage()->read();
    }

    protected function _getAcl()
    {
        return Zend_Registry::get('acl');
    }
}
