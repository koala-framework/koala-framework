<?php
class Kwf_Controller_Action_User_BackendActivateController extends Kwf_Controller_Action
{
    public function preDispatch()
    {
        $this->getHelper('viewRenderer')->setNoController(true);
        $this->getHelper('viewRenderer')->setViewScriptPathNoControllerSpec('user/:action.:suffix');
        if (!$this->_getParam('user') && $this->getRequest()->getActionName() != 'error') {

            if ($this->getRequest()->getActionName() == 'redirect-callback') {
                $state = explode('-', $this->_getParam('state'));
                if (count($state) != 3) throw new Kwf_Exception_NotFound();
                $code = $state[2];
            } else {
                $code = $this->_getParam('code');
            }

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
        $this->view->dep = Kwf_Assets_Package_Default::getInstance('Admin');

        //parent::preDispatch();
    }


    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setModel(new Kwf_Model_FnF());
    }

    public function indexAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('activate');
        $this->view->email = $this->_getParam('user')->email;
        $this->view->isActivate = $this->_getParam('user')->isActivated();

        $users = Zend_Registry::get('userModel');

        $showPassword = false;

        //is there a password auth?
        foreach ($users->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                $showPassword = true;
            }
        }

        //if a redirect auth doesn't allow password hide it
        foreach ($users->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                if (!$auth->allowPasswordForUser($this->getParam('user'))) {
                    $showPassword = false;
                }
            }
        }

        $this->view->redirects = array();
        if ($showPassword) {
            $url = $this->getFrontController()->getRouter()->assemble(array(
                'controller' => 'backend-change-password',
                'action' => 'index',
            ), 'kwf_user');
            $url .= '?code='.$this->_getParam('code');
            $this->view->redirects[] = array(
                'url' => $url,
                'name' => trlKwf('Password')
            );
        }

        foreach ($users->getAuthMethods() as $k=>$auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect && $auth->showInBackend()) {
                $url = $this->getFrontController()->getRouter()->assemble(array(
                    'controller' => 'backend-activate',
                    'action' => 'redirect',
                ), 'kwf_user');
                $label = $auth->getLoginRedirectLabel();
                $this->view->redirects[] = array(
                    'url' => $url.'?authMethod='.$k.'&code='.$this->_getParam('code'),
                    'name' => Kwf_Trl::getInstance()->trlStaticExecute($label['name'])
                );
            }
        }

        if (count($this->view->redirects) == 1 && $showPassword) {
            $this->redirect($this->view->redirects[0]['url']);
        }
    }

    private function _getRedirectBackUrl()
    {
        $redirectBackUrl = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'backend-activate',
            'action' => 'redirect-callback',
        ), 'kwf_user');
        $redirectBackUrl = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'
            .$_SERVER['HTTP_HOST']
            .$redirectBackUrl;
        return $redirectBackUrl;
    }

    public function redirectAction()
    {
        $authMethod = $this->_getParam('authMethod');
        $users = Zend_Registry::get('userModel');
        $authMethods = $users->getAuthMethods();
        if (!isset($authMethods[$authMethod])) {
            throw new Kwf_Exception_NotFound();
        }

        $f = new Kwf_Filter_StrongRandom();
        $state = $authMethod.'-'.$f->filter(null).'-'.$this->_getParam('code');

        //save state in namespace to validate it later
        $ns = new Kwf_Session_Namespace('kwf-backend-activate');
        $ns->state = $state;

        $url = $authMethods[$authMethod]->getLoginRedirectUrl($this->_getRedirectBackUrl(), $state);
        $this->redirect($url);
    }

    public function redirectCallbackAction()
    {
        $state = $this->_getParam('state');

        $ns = new Kwf_Session_Namespace('kwf-backend-activate');
        if (!$ns->state || $state != $ns->state) throw new Kwf_Exception_AccessDenied();

        $state = explode('-', $state);
        if (count($state) != 3) throw new Kwf_Exception_NotFound();
        $authMethod = $state[0];
        $code = $state[2];

        $users = Zend_Registry::get('userModel');
        $authMethods = $users->getAuthMethods();
        if (!isset($authMethods[$authMethod])) {
            throw new Kwf_Exception_NotFound();
        }
        $user = $this->_getParam('user');
        $authMethods[$authMethod]->associateUserByParams($user, $this->_getRedirectBackUrl(), $this->getRequest()->getParams());
        $user->clearActivationToken();
        $users->loginUserRow($user, true);

        $this->redirect('/kwf/welcome');
    }

    public function errorAction()
    {
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('activate-error');
        $this->view->errorMessage = $this->_getParam('errorMessage');
    }
}
