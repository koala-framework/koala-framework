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
        $this->view->applicationName = Kwf_Config::getValue('application.name');
        $this->view->brandingKoala = Kwf_Config::getValue('application.branding.koala');
        $this->view->brandingVividPlanet = Kwf_Config::getValue('application.branding.vividPlanet');
        $this->view->pages = Kwf_Registry::get('acl')->has('kwf_component_pages');
        $this->view->baseUrl = Kwf_Setup::getBaseUrl();
        $this->view->favicon = Kwf_View_Ext::getFavicon();

        try {
            $t = new Kwf_Util_Model_Welcome();
            $row = $t->getRow(1);
        } catch (Zend_Db_Statement_Exception $e) {
            //wenn tabelle nicht existiert fehler abfangen
            $row = null;
        }
        if ($row && $fileRow = $row->getParentRow('LoginImage')) {
            $this->view->image = Kwf_Media::getUrlByRow(
                    $row, 'LoginImageLarge', 'login'
            );

            $this->view->imageSize = Kwf_Media_Image::calculateScaleDimensions(
                $fileRow->getImageDimensions(),
                Kwf_Util_Model_Welcome::getImageDimensions('LoginImageLarge')
            );
        } else {
            $this->view->image = false;
        }
        if (Kwf_Registry::get('config')->allowUntagged === true) {
            if (file_exists('.git') && Kwf_Util_Git::web()->getActiveBranch() != 'production') {
                $this->view->untagged = true;
            }
            if (file_exists(KWF_PATH.'/.git') && Kwf_Util_Git::kwf()->getActiveBranch() != 'production/'.Kwf_Registry::get('config')->application->id) {
                $this->view->untagged = true;
            }
        }
        
        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('login');
        $this->view->lostPasswordLink = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'login',
            'action' => 'lost-password',
        ), 'kwf_user');


        $this->view->redirects = array();
        $users = Zend_Registry::get('userModel');
        foreach ($users->getAuthMethods() as $k=>$auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect && $auth->showInBackend()) {
                $url = $this->getFrontController()->getRouter()->assemble(array(
                    'controller' => 'backend-login',
                    'action' => 'redirect',
                ), 'kwf_user');
                $label = $auth->getLoginRedirectLabel();
                $this->view->redirects[] = array(
                    'url' => $url.'?authMethod='.$k.'&redirect='.urlencode($_SERVER['REQUEST_URI']),
                    'name' => Kwf_Trl::getInstance()->trlStaticExecute($label['name'])
                );
            }
        }

        parent::indexAction();
    }

    private function _getRedirectBackUrl()
    {
        $redirectBackUrl = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'backend-login',
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
        $state = $authMethod.'-'.$f->filter(null).'-'.$this->_getParam('redirect');

        //save state in namespace to validate it later
        $ns = new Kwf_Session_Namespace('kwf-backend-login');
        $ns->state = $state;

        $url = $authMethods[$authMethod]->getLoginRedirectUrl($this->_getRedirectBackUrl(), $state);
        $this->redirect($url);
    }

    public function redirectCallbackAction()
    {
        $state = $this->_getParam('state');

        $ns = new Kwf_Session_Namespace('kwf-backend-login');
        if (!$ns->state || $state != $ns->state) throw new Kwf_Exception_AccessDenied();

        $state = explode('-', $state);

        if (count($state) != 3) throw new Kwf_Exception_NotFound();
        $authMethod = $state[0];
        $redirect = $state[2];

        $users = Zend_Registry::get('userModel');
        $authMethods = $users->getAuthMethods();
        if (!isset($authMethods[$authMethod])) {
            throw new Kwf_Exception_NotFound();
        }
        $user = $authMethods[$authMethod]->getUserToLoginByParams($this->_getRedirectBackUrl(), $this->getRequest()->getParams());
        if ($user) {
            $users->loginUserRow($user, true);
        } else {
            throw new Kwf_Exception_AccessDenied();
        }

        if ($redirect == 'jsCallback') {
            echo "<script type=\"text/javascript\">\n";
            echo "window.opener.ssoCallback('".Kwf_Util_SessionToken::getSessionToken()."');\n";
            echo "window.close();\n";
            echo "</script>\n";
            exit;
        } else {
            Kwf_Util_Redirect::redirect($redirect);
        }
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
            $redirectUrl = $this->getRequest()->getPathInfo();
            if ($this->_getParam('redirect') && substr($this->_getParam('redirect'), 0, 1) == '/') {
                $redirectUrl = $this->_getParam('redirect');
            }
            $this->redirect($redirectUrl);
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
