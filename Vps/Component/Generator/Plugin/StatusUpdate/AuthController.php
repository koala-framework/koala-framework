<?php
class Vps_Component_Generator_Plugin_StatusUpdate_AuthController extends Vps_Controller_Action
{
    private function _getBackend()
    {
        $callbackUrl = 'http://';
        if (isset($_SERVER['HTTP_HOST'])) {
            $callbackUrl .= $_SERVER['HTTP_HOST'];
        } else {
            $callbackUrl .= Vps_Registry::get('config')->server->domain;
        }
        $callbackUrl .= Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Auth');
        $callbackUrl .= '/callback?componentId='.$this->_getParam('componentId');
        $callbackUrl .= '&backend='.$this->_getParam('backend');

        $backends = Vpc_Abstract::getSetting($this->_getParam('class'), 'backends');
        $backend = $backends[$this->_getParam('backend')];
        return new $backend($callbackUrl);
    }

    public function requestAction()
    {
        $backend = $this->_getBackend();
        if ($backend->isAuthed()) throw new Vps_Exception("Already Authenticated");
        header('Location: '.$backend->getAuthUrl());
        exit;
    }

    public function callbackAction()
    {
        $backend = $this->_getBackend();
        $backend->processCallback($this->_request->getQuery());
        echo "<script type=\"text/javascript\">\n";
        echo "window.opener.authCallback.call(window.opener.authCallbackScope);\n";
        echo "window.close();\n";
        echo "</script>\n";
        exit;
    }
}
