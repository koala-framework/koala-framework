<?php
abstract class Kwf_Controller_Action extends Zend_Controller_Action
{
    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    public function preDispatch()
    {
        Kwf_Util_Https::ensureHttps();

        if ($this->_getParam('application_max_assets_mtime')
                && $this->getHelper('ViewRenderer')->isJson()) {
            $l = new Kwf_Assets_Loader();
            if ($l->getDependencies()->getMaxFileMTime()!= $this->_getParam('application_max_assets_mtime')) {
                $this->_forward('json-wrong-version', 'error',
                                    'kwf_controller_action_error');
                return;
            }
        }

        $allowed = false;
        if ($this->_getUserRole() == 'cli') {
            $allowed = true;
        } else {
            $acl = $this->_getAcl();
            $resource = $this->getRequest()->getResourceName();
            $aclResource = $acl->get($resource);
            if ($resource == 'kwf_user_changeuser') {
                //spezielle berechtigungsabfrage für Benutzerwechsel
                $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
                $allowed = $acl->isAllowed($role, $resource, 'view');
            } else if ($resource == 'kwf_component') {
                $allowed = $this->_isAllowedComponent(); // Bei Test ist niemand eingeloggt und deshalb keine Prüfung
            } else if ($aclResource instanceof Kwf_Acl_Resource_ComponentId_Interface) {
                if (!$this->_getParam('componentId')) throw new Kwf_Exception('componentId is not set');
                $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                    $this->_getParam('componentId'), array('ignoreVisible' => true)
                );
                foreach($components as $component) {
                    $allowed = $acl->getComponentAcl()->isAllowed($this->_getAuthData(), $component);
                    if ($allowed) break;
                }
            } else {
                if (!$acl->has($resource)) {
                    throw new Kwf_Exception_NotFound();
                } else {
                    if ($this->_getAuthData()) {
                        $allowed = $acl->isAllowedUser($this->_getAuthData(), $resource, 'view');
                    } else {
                        $allowed = $acl->isAllowed($this->_getUserRole(), $resource, 'view');
                    }
                }
            }
            if ($allowed) {
                $allowed = $this->_isAllowed($this->_getAuthData());
            }
        }

        if (!$allowed) {
            $params = array(
                'resource' => $resource,
                'role' => $this->_getUserRole()
            );
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('json-login', 'login',
                                    'kwf_controller_action_user', $params);
            } else {
                $params = array('location' => $this->getRequest()->getPathInfo());
                $this->_forward('index', 'login',
                                    'kwf_controller_action_user', $params);
            }
        }
    }

    protected function _isAllowed($user)
    {
        return true;
    }

    protected function _isAllowedComponent()
    {
        $actionName = $this->getRequest()->getActionName();
        if ($actionName != 'json-index' && $actionName != 'index') {
            $authData = $this->_getAuthData();
            $class = $this->_getParam('class');
            $componentId = $this->_getParam('componentId');
            if (!$componentId) {
                return Kwf_Registry::get('acl')->isAllowedComponent($class, $authData);
            } else {
                return Kwf_Registry::get('acl')->isAllowedComponentById($componentId, $class, $authData);
            }
        }
        return true;
    }

    public function postDispatch()
    {
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Component_Cache::getInstance()->writeBuffer();
    }

    protected function _getUserRole()
    {
        if (php_sapi_name() == 'cli') return 'cli';
        return Kwf_Registry::get('userModel')->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        if (php_sapi_name() == 'cli') return null;
        return Kwf_Registry::get('userModel')->getAuthedUser();
    }
    /**
     * @return Kwf_Acl
     */
    protected function _getAcl()
    {
        return Zend_Registry::get('acl');
    }
}
