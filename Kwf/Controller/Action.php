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

    protected function _validateCsrf()
    {
        if ($this->_helper->getHelper('viewRenderer')->isJson()) {
            if ($this->getRequest()->getHeader('X-Requested-With') != 'XMLHttpRequest') {
                throw new Kwf_Exception("Missing X-Requested-With header (json urls must be called using XHR only to prevent CSRF)");
            }
            if (!$this->getRequest()->getHeader('Referer')) {
                throw new Kwf_Exception("Missing Referer header");
            }
            $requiredReferer = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$this->getRequest()->getHeader('Host').'/';
            if (substr($this->getRequest()->getHeader('Referer'), 0, strlen($requiredReferer)) != $requiredReferer) {
                throw new Kwf_Exception("Invalid Referer");
            }
        }
    }

    public function preDispatch()
    {
        $this->_validateCsrf();

        $t = microtime(true);
        $allowed = $this->_isAllowedResource();
        if ($allowed) {
            $allowed = $this->_isAllowed($this->_getAuthData());
        }

        if (!$allowed) {
            $params = array(
                'role' => $this->_getUserRole()
            );
            if ($this->getHelper('ViewRenderer')->isJson()) {
                $this->_forward('json-login', 'login',
                                    'kwf_controller_action_user', $params);
            } else {
                $params = array('location' => '/' . ltrim($this->getRequest()->getPathInfo(), '/'));
                $this->_forward('index', 'login',
                                    'kwf_controller_action_user', $params);
            }
        }

        Kwf_Benchmark::subCheckpoint('check acl', microtime(true)-$t);
    }

    protected function _isAllowedResource()
    {
        $allowed = false;
        if ($this->_getUserRole() == 'cli') {
            $allowed = true;
        } else {
            $acl = $this->_getAcl();
            $resource = $this->getRequest()->getResourceName();
            if ($resource == 'kwf_component') {
                $allowed = $this->_isAllowedComponent();
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
        }
        return $allowed;
    }

    protected function _isAllowed($user)
    {
        return true;
    }

    protected function _isAllowedComponent()
    {
        $authData = $this->_getAuthData();
        $class = $this->_getParam('class');
        $componentId = $this->_getParam('componentId');
        if (!$componentId) {
            return Kwf_Registry::get('acl')->isAllowedComponent($class, $authData);
        } else {
            return Kwf_Registry::get('acl')->isAllowedComponentById($componentId, $class, $authData);
        }
    }

    public function postDispatch()
    {
        Kwf_Events_ModelObserver::getInstance()->process();
        Kwf_Component_Cache::getInstance()->writeBuffer();
    }

    protected function _getUserRole()
    {
        if (PHP_SAPI == 'cli') return 'cli';
        $um = Kwf_Registry::get('userModel');
        if (!$um) return null;
        return $um->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        if (PHP_SAPI == 'cli') return null;
        $um = Kwf_Registry::get('userModel');
        if (!$um) return null;
        return $um->getAuthedUser();
    }
    /**
     * @return Kwf_Acl
     */
    protected function _getAcl()
    {
        return Zend_Registry::get('acl');
    }
}
