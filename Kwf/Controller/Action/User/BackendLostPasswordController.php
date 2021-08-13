<?php
class Kwf_Controller_Action_User_BackendLostPasswordController extends Kwf_Controller_Action_Form_Controller
{
    protected $_form = 'Kwc_User_LostPassword_Form_FrontendForm';

    public function preDispatch()
    {
        Kwf_Util_BackendLoginRestriction::isAllowed();

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
