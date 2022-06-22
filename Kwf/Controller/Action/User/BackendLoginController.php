<?php
class Kwf_Controller_Action_User_BackendLoginController extends Kwf_Controller_Action_Form_Controller
{
    protected $_form = 'Kwc_User_Login_Form_FrontendForm';

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
            if (file_exists('.git') && (strpos(Kwf_Util_Git::web()->getActiveBranch(), 'production') === false)) {
                $this->view->untagged = true;
            }
        }

        $this->view->contentScript = $this->getHelper('viewRenderer')->getViewScript('login');
        $this->view->lostPasswordLink = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'login',
            'action' => 'lost-password',
        ), 'kwf_user');


        $this->view->redirects = array();
        $authMethods = Zend_Registry::get('userModel')->getAuthMethods();
        foreach ($authMethods as $k=>$auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect && $auth->showInBackend()) {
                $url = $this->getFrontController()->getRouter()->assemble(array(
                    'controller' => 'backend-login',
                    'action' => 'redirect',
                ), 'kwf_user');
                $label = $auth->getLoginRedirectLabel();
                $this->view->redirects[] = array(
                    'url' => $url,
                    'authMethod' => $k,
                    'redirect' => $_SERVER['REQUEST_URI'],
                    'name' => Kwf_Trl::getInstance()->trlStaticExecute($label['name']),
                    'icon' => isset($label['icon']) ? '/assets/'.$label['icon'] : false,
                    'formOptionsHtml' => Kwf_User_Auth_Helper::getRedirectFormOptionsHtml($auth->getLoginRedirectFormOptions()),
                );
            }
        }

        if (count($authMethods) == 1 && count($this->view->redirects) == 1) {
            $r = $this->view->redirects[0];
            $url = $r['url'];
            $url .= '?authMethod=' . Kwf_Util_HtmlSpecialChars::filter($r['authMethod']);
            $url .= '&redirect=' . Kwf_Util_HtmlSpecialChars::filter($r['redirect']);
            Kwf_Util_Redirect::redirect($url);
        }

        parent::indexAction();
    }

    private function _getRedirectBackUrl()
    {
        $redirectBackUrl = $this->getFrontController()->getRouter()->assemble(array(
            'controller' => 'login',
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
        $state = 'login.'.$authMethod.'.'.$f->filter(null).'.'.urlencode(str_replace('.', 'kwfdot', $this->_getParam('redirect')));

        //save state in namespace to validate it later
        $ns = new Kwf_Session_Namespace('kwf-login-redirect');
        $ns->state = $state;

        $formValues = array();
        foreach ($authMethods[$authMethod]->getLoginRedirectFormOptions() as $option) {
            if ($option['type'] == 'select') {
                $formValues[$option['name']] = $this->_getParam($option['name']);
            }
        }

        $url = $authMethods[$authMethod]->getLoginRedirectUrl($this->_getRedirectBackUrl(), $state, $formValues);
        if (!$url) {
            $html = $authMethods[$authMethod]->getLoginRedirectHtml($this->_getRedirectBackUrl(), $state, $formValues);
            echo $html;
            exit;
        }
        $this->redirect($url);
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
            $redirectUrl = '/'.ltrim($this->getRequest()->getPathInfo(), '/');
            if ($this->_getParam('redirect') && substr($this->_getParam('redirect'), 0, 1) == '/') {
                $redirectUrl = $this->_getParam('redirect');
            }
            $this->redirect($redirectUrl);
        } else {
            $errors = $this->getRequest()->getParam('formErrors');
            foreach ($result->getMessages() as $msg) {
                $errors[] = array(
                    'message' =>  Kwf_Trl::getInstance()->trlStaticExecute($msg)
                );
            }
            $this->getRequest()->setParam('formErrors', $errors);

            $this->_showForm();
        }
    }
}
