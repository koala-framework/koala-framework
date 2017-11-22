<?php
class Kwf_Controller_Action_User_BackendChangePasswordController extends Kwf_Controller_Action_Form_Controller
{
    protected $_form = 'Kwc_User_Activate_Form_FrontendForm';

    public function preDispatch()
    {
        $this->getHelper('viewRenderer')->setNoController(true);
        $this->getHelper('viewRenderer')->setViewScriptPathNoControllerSpec('user/:action.:suffix');
        if (!$this->_getParam('user') && $this->getRequest()->getActionName() != 'error') {
            $code = $this->_getParam('code');
            if (!preg_match('#^(.*)-(\w*)$#', $code, $m)) {
                $this->getRequest()->setParam('errorMessage', trlKwf("Activation code is invalid. Maybe the URL wasn't copied completely?"));
                $this->forward('error');
            } else {
                $userId = $m[1];
                $code = $m[2];
                $userModel = Zend_Registry::get('userModel');
                $user = $userModel->getRow($userId);
                $this->getRequest()->setParam('user', $user);
                if (!$user) {
                    $this->getRequest()->setParam('errorMessage', trlKwf("Activation code is invalid. Maybe the URL wasn't copied completely?"));
                    $this->forward('error');
                } else if (!$user->validateActivationToken($code) && $user->isActivated()) {
                    $this->getRequest()->setParam('errorMessage', trlKwf("This account has already been activated."));
                    $this->forward('error');
                } else if (!$user->validateActivationToken($code)) {
                    $this->getRequest()->setParam('errorMessage', trlKwf("Activation code is invalid. Maybe your account has already been activated, the URL was not copied completely, or the password has already been set?"));
                    $this->forward('error');
                }
            }
        }
        parent::preDispatch();
    }

    protected function _isAllowedResource()
    {
        return true;
    }

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setModel(new Kwf_Model_FnF());
    }


    public function indexAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('change-password');
        $this->view->email = $this->_getParam('user')->email;
        $this->view->isActivated = $this->_getParam('user')->isActivated();
        parent::indexAction();
    }


    protected function _afterSave($row)
    {
        $user = $this->_getParam('user');
        $userModel = $user->getModel();
        $userModel->setPassword($user, $row->password);
        $user->clearActivationToken();
        $this->redirect('/kwf/welcome');
        parent::_afterSave($row);
    }

    public function successAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('activate-success');
    }

    public function errorAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('activate-error');
        $this->view->errorMessage = $this->_getParam('errorMessage');
    }
}
