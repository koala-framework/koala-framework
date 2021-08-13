<?php
class Kwf_Controller_Action_User_BackendLostPasswordController extends Kwf_Controller_Action_Form_Controller
{
    protected $_form = 'Kwc_User_LostPassword_Form_FrontendForm';

    public function preDispatch()
    {
        if (Kwf_Config::getValue('blockExternalAdminAccess')) {
            $currentIp = $_SERVER['REMOTE_ADDR'];
            $valid = false;
            foreach (Kwf_Config::getValueArray('allowedAdminIPs') as $ip) {
                if (substr($ip, -1)=='*') {
                    $i = substr($ip, 0, -1);
                    if (substr($currentIp, 0, strlen($i)) == $i){
                        $valid = true;
                    }
                } else if (substr($ip, 0, 1)=='*') {
                    $i = substr($ip, 1);
                    if (substr($currentIp, -strlen($i)) == $i){
                        $valid = true;
                    }
                } else {
                    if ($currentIp == $ip){
                        $valid = true;
                    }
                }
            }
            if (!$valid) {
                throw new Kwf_Exception_AccessDenied();
            }
        }

        $this->getHelper('viewRenderer')->setNoController(true);
        $this->getHelper('viewRenderer')->setViewScriptPathNoControllerSpec('user/:action.:suffix');
        parent::preDispatch();
    }

    protected function _isAllowedResource()
    {
        return true;
    }

    public function indexAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('lost-password');
        parent::indexAction();
    }

    public function successAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('lost-password-success');
    }
}
