<?php
class Kwf_Controller_Action_User_BackendLoginController extends Kwf_Controller_Action_Form_Controller
{
    protected $_form = 'Kwc_User_Login_Form_FrontendForm';

    public function preDispatch()
    {
        $this->getHelper('viewRenderer')->setNoController(true);
        $this->getHelper('viewRenderer')->setViewScriptPathNoControllerSpec('user/:action.:suffix');
        parent::preDispatch();
    }

    public function indexAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('login');
        $this->view->lostPasswordLink = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'login',
            'action' => 'lost-password',
        ), 'kwf_user');
        parent::indexAction();
    }


    protected function _initFields()
    {
        parent::_initFields();
        unset($this->_form->fields['auto_login']);
    }

    protected function _afterSave($row)
    {
        $row = $this->_getParam('row');

        $adapter = new Kwf_Auth_Adapter_PasswordAuth();
        $auth = Kwf_Auth::getInstance();
        $adapter->setIdentity($row->email);
        $adapter->setCredential($row->password);
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $this->redirect($this->getRequest()->getPathInfo());
        } else {
            $errors = $this->getRequest()->getParam('formErrors');
            foreach ($result->getMessages() as $msg) {
                $errors[] = array(
                    'message' => $msg
                );
            }
            $this->getRequest()->setParam('formErrors', $errors);

            $this->_showForm();
        }
    }
}
