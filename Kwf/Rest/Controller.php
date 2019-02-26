<?php
abstract class Kwf_Rest_Controller extends Zend_Rest_Controller
{
    public function preDispatch()
    {
        $this->_validateCsrf();

        $allowed = false;
        if ($this->_getUserRole() == 'cli') {
            $allowed = true;
        } else {
            $acl = Zend_Registry::get('acl');
            $resource = $this->getRequest()->getResourceName();
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

        if (!$allowed) {
            $params = array(
                'resource' => $resource,
                'role' => $this->_getUserRole()
            );
            $this->_forward('json-login', 'login',
                                'kwf_controller_action_user', $params);
        }

        parent::preDispatch();
    }

    protected function _validateCsrf()
    {
        if (!$this->getRequest()->getHeader('X-Requested-With')) {
            throw new Kwf_Exception("Missing X-Requested-With header (rest urls must be called using XHR only to prevent CSRF)");
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
        return Kwf_Registry::get('userModel')->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        if (PHP_SAPI == 'cli') return null;
        return Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
