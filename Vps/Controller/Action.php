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
                return;
            }
        }

        $acl = $this->_getAcl();
        $resource = $this->getRequest()->getResourceName();
        if ($resource == 'vps_user_changeuser') {
            //spezielle berechtigungsabfrage für Benutzerwechsel
            $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
            $allowed = $acl->isAllowed($role, $resource, 'view');
        } else if ($this->_getUserRole() == 'cli') {
            $allowed = $acl->isAllowed('cli', $resource, 'view');
        } else {
            $allowed = $acl->isAllowedUser($this->_getAuthData(), $resource, 'view');
        }
        if ($allowed) {
            $allowed = $this->_isAllowedComponent();
        }
        if ($allowed) {
            if ($this->_getUserRole() == 'cli') {
                $allowed = $this->_isAllowed('cli');
            } else {
                $allowed = $this->_isAllowed($this->_getAuthData());
            }
        }

        if (!$allowed) {
            $role = null;
            if ($this->_getAuthData()) $role = $this->_getAuthData()->role;
            $params = array(
                'resource' => $resource,
                'role' => $role
            );
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('json-login', 'login',
                                    'vps_controller_action_user', $params);
            } else {
                $params = array('location' => $this->getRequest()->getPathInfo());
                $this->_forward('index', 'login',
                                    'vps_controller_action_user', $params);
            }
        }
    }

    protected function _isAllowed($user)
    {
        return true;
    }

    protected function _isAllowedComponent()
    {
        $allowed = true;
        $resource = $this->getRequest()->getResourceName();
        if ($resource == 'vps_component') {
            $a = $this->getRequest()->getActionName();
            if ($a != 'json-index' && $a != 'index') {
                if (!$this->_getParam('componentId')) {
                    $allowed = false;
                    foreach (Vps_Registry::get('acl')->getAllResources() as $r) {
                        if ($r instanceof Vps_Acl_Resource_ComponentClass_Interface) {
                            if ($this->_getParam('class') == $r->getComponentClass()) {
                                $allowed = Vps_Registry::get('acl')->getComponentAcl()
                                    ->isAllowed($this->_getAuthData(), $this->_getParam('class'));
                                break;
                            }
                        }
                    }
                } else {
                    $components = Vps_Component_Data_Root::getInstance()
                        ->getComponentsByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
                    if (empty($components)) {
                        throw new Vps_Exception("Can't find component to check permissions");
                    }
                    $allowed = false;
                    foreach ($components as $component) {
                        if (Vps_Registry::get('acl')->getComponentAcl()
                            ->isAllowed($this->_getAuthData(), $component)
                        ) {
                            $allowed = true;
                        }
                    }
                }
            }
        }
        return $allowed;
    }

    public function postDispatch()
    {
        if (Zend_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component) {
            Vps_Component_RowObserver::getInstance()->process();
        }
    }

    protected function _getUserRole()
    {
        return Vps_Registry::get('userModel')->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        return Vps_Registry::get('userModel')->getAuthedUser();
    }
    /**
     * @return Vps_Acl
     */
    protected function _getAcl()
    {
        return Zend_Registry::get('acl');
    }
}
