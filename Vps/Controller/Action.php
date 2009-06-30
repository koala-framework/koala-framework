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
        } else if ($resource == 'vps_component') {
            $allowed = $this->_isAllowedComponent();
        } else {
            $allowed = $acl->isAllowedUser($this->_getAuthData(), $resource, 'view');
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
        $actionName = $this->getRequest()->getActionName();
        if ($actionName != 'json-index' && $actionName != 'index') {
            if (!$this->_getParam('componentId')) {
                $allowed = false;
                $class = $this->_getParam('class');
                foreach (Vps_Registry::get('acl')->getAllResources() as $r) {
                    if ($r instanceof Vps_Acl_Resource_ComponentClass_Interface) {
                        if ($class == $r->getComponentClass()) {
                            $allowed = Vps_Registry::get('acl')->getComponentAcl()
                                ->isAllowed($this->_getAuthData(), $this->_getParam('class'));
                            break;
                        }
                    }
                }
            } else {

                $allowed = $this->_isAllowedComponentById($this->_getParam('componentId'));

            }
        }
        return $allowed;
    }

    protected function _isAllowedComponentById($componentId, $class = null)
    {
        $allowed = false;

        if (!$class) $class = $this->_getParam('class');

        $components = Vps_Component_Data_Root::getInstance()
            ->getComponentsByDbId($componentId, array('ignoreVisible'=>true));
        if (!$components) {
            throw new Vps_Exception("Can't find component to check permissions");
        }

        // sobald man eine bearbeiten darf, darf man alle bearbeiten
        // zB wenn man bei proSalzburg und proPona gleichzeitig drin ist
        foreach ($components as $component) {
            // Checken, ob übergebene componentClass auf der aktuellen Page vorkommen kann
            $allowCheck = false;

            if ($component->componentClass == $class) $allowCheck = true;
            $c = $component->parent;

            $stopComponent = $component->getPage();
            if (!is_null($stopComponent)) $stopComponent = $stopComponent->parent;

            while (!$allowCheck && $c && !$c->componentId != $stopComponent->componentId) {
                $allowedComponentClasses = Vpc_Abstract::getChildComponentClasses(
                    $c->componentClass, array('page' => false)
                );
                if (in_array($class, $allowedComponentClasses))
                    $allowCheck = true;
                $c = $c->parent;
            }

            if ($allowCheck &&
                Vps_Registry::get('acl')->getComponentAcl()
                    ->isAllowed($this->_getAuthData(), $component)
            ) {
                $allowed = true;
            }

            if ($allowed) return $allowed;
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
        if (php_sapi_name() == 'cli') return 'cli';
        return Vps_Registry::get('userModel')->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        if (php_sapi_name() == 'cli') return null;
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
