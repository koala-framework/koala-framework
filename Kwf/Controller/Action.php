<?php
abstract class Kwf_Controller_Action extends Zend_Controller_Action
{
    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        Kwf_Benchmark::checkpoint('Action::init');
    }

    //copied from zend to insert benchmark checkpoints
    public function dispatch($action)
    {
        // Notify helpers of action preDispatch state
        $this->_helper->notifyPreDispatch();

        $this->preDispatch();
        Kwf_Benchmark::checkpoint('Action::preDispatch');
        if ($this->getRequest()->isDispatched()) {
            if (null === $this->_classMethods) {
                $this->_classMethods = get_class_methods($this);
            }

            // preDispatch() didn't change the action, so we can continue
            if ($this->getInvokeArg('useCaseSensitiveActions') || in_array($action, $this->_classMethods)) {
                if ($this->getInvokeArg('useCaseSensitiveActions')) {
                    trigger_error('Using case sensitive actions without word separators is deprecated; please do not rely on this "feature"');
                }
                $this->$action();
            } else {
                $this->__call($action, array());
            }
            Kwf_Benchmark::checkpoint('Action::action');

            $this->postDispatch();
            Kwf_Benchmark::checkpoint('Action::postDispatch');
        }

        // whats actually important here is that this action controller is
        // shutting down, regardless of dispatching; notify the helpers of this
        // state
        $this->_helper->notifyPostDispatch();
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

        if ($this->_helper->getHelper('viewRenderer')->isJson() && Kwf_Util_SessionToken::getSessionToken()) {
            if (!$this->_getParam('kwfSessionToken')) {
                throw new Kwf_Exception("Missing sessionToken parameter");
            }
            if ($this->_getParam('kwfSessionToken') != Kwf_Util_SessionToken::getSessionToken()) {
                throw new Kwf_Exception("Invalid kwfSessionToken");
            }
        }

        $t = microtime(true);
        $allowed = false;
        if ($this->_getUserRole() == 'cli') {
            $allowed = true;
        } else {
            $acl = $this->_getAcl();
            $resource = $this->getRequest()->getResourceName();
            if ($resource == 'kwf_user_changeuser') {
                //spezielle berechtigungsabfrage für Benutzerwechsel
                $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
                $allowed = $acl->isAllowed($role, $resource, 'view');
            } else if ($resource == 'kwf_component') {
                $allowed = $this->_isAllowedComponent(); // Bei Test ist niemand eingeloggt und deshalb keine Prüfung
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
                $params = array('location' => $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo());
                $this->_forward('index', 'login',
                                    'kwf_controller_action_user', $params);
            }
        }

        Kwf_Benchmark::subCheckpoint('check acl', microtime(true)-$t);
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
