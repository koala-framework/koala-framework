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
        $resource = $this->_getResourceName();
        if ($resource == 'vps_user_changeuser') {
            //spezielle berechtigungsabfrage für Benutzerwechsel
            $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
            $allowed = $acl->isAllowed($role, $resource, 'view');
        } else if ($this->_getUserRole() == 'cli') {
            $allowed = $acl->isAllowed('cli', $resource, 'view');
        } else {
            $allowed = $acl->isAllowedUser($this->_getAuthData(), $resource, 'view');
        }

        if (!$allowed) {
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
    
    public function postDispatch()
    {
        if (Zend_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component) {
            Vps_Component_RowObserver::getInstance()->process();
        }
    }

    protected function _getResourceName()
    {
        if (isset($_SERVER['SHELL'])) return 'vps_cli';
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
        if (php_sapi_name() == 'cli') return 'cli';
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
