<?php
abstract class Kwf_Rest_Controller extends Zend_Rest_Controller
{
    public function preDispatch()
    {
        Kwf_Util_Https::ensureHttps();

        if ($this->_getParam('applicationAssetsVersion')) {
            if (Kwf_Assets_Dispatcher::getAssetsVersion() != $this->_getParam('applicationAssetsVersion')) {
                $this->_forward('json-wrong-version', 'error',
                                    'kwf_controller_action_error');
                return;
            }
        }

        $this->_validateSessionToken();

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

    protected function _validateSessionToken()
    {
        if (Kwf_Util_SessionToken::getSessionToken()) {
            if (!$this->_getParam('kwfSessionToken') && !$this->getRequest()->getHeader('X-Kwf-Session-Token')) {
                throw new Kwf_Exception("Missing sessionToken parameter or X-Kwf-Session-Token header");
            }
            if (($this->_getParam('kwfSessionToken') != Kwf_Util_SessionToken::getSessionToken())
                &&  ($this->getRequest()->getHeader('X-Kwf-Session-Token') != Kwf_Util_SessionToken::getSessionToken())
            ) {
                throw new Kwf_Exception("Invalid kwfSessionToken");
            }
        }
    }

    public function postDispatch()
    {
        Kwf_Events_ModelObserver::getInstance()->process();
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
}
