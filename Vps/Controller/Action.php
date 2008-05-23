<?php
abstract class Vps_Controller_Action extends Zend_Controller_Action
{
    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    public function preDispatch()
    {
        if (!$this instanceof Vps_Controller_Action_Error_ErrorController
                && $this->_getParam('application_version')
                && $this->getHelper('ViewRenderer')->isJson()) {
            $version = Zend_Registry::get('config')->application->version;
            if ($version != $this->_getParam('application_version')) {
                $this->_forward('json-wrong-version', 'error',
                                    'vps_controller_action_error');
            }
        }

        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        $resource = $this->_getResourceName();
        if ($resource == 'vps_user_changeuser') {
            //spezielle berechtigungsabfrage für Benutzerwechsel
            $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
        }

        if (!$acl->isAllowed($role, $resource, 'view')) {
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('json-login', 'login',
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
        if ($this->getRequest()->getControllerName() == 'component') {
            $resource = 'vps_component';
        } else {
            $resource = strtolower(str_replace(array('Vps_Controller_Action_',
                                                    'Controller'),
                                            '', get_class($this)));
            if (substr(get_class($this), 0, 4) == 'Vps_') {
                $resource = 'vps_'.$resource;
            }
        }
        return $resource;
    }

    protected function _getUserRole()
    {
        if (isset($_SERVER['SHELL'])) return 'cli';
        return Zend_Registry::get('userModel')->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        return Zend_Registry::get('userModel')->getAuthedUser();
    }

    protected function _getAcl()
    {
        return Zend_Registry::get('acl');
    }
}
