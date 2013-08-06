<?php
abstract class Kwf_Controller_Plugin_Acl_Abstract extends Zend_Controller_Plugin_Abstract
{
    protected $_acl;

    public function __construct(Kwf_Acl $acl)
    {
        $this->_acl = $acl;
    }

    protected function _forwardLogin(Zend_Controller_Request_Abstract $request)
    {
        $request->setModuleName('kwf_controller_action_user');
        $request->setControllerName('login');
        $request->setDispatched(false);
        if (substr($request->getActionName(), 0, 4) == 'json') {
            $request->setActionName('json-login');
        } else {
            $params = array('location' => $request->getBaseUrl().$request->getPathInfo());
            $request->setParams($params);
            $request->setActionName('index');
        }
    }

    protected function _getAuthedUserRole()
    {
        if (php_sapi_name() == 'cli') return 'cli';
        return Zend_Registry::get('userModel')->getAuthedUserRole();
    }
    protected function _getAuthedUser()
    {
        if (php_sapi_name() == 'cli') return null;
        return Zend_Registry::get('userModel')->getAuthedUser();
    }

}
