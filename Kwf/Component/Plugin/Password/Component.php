<?php
class Kwf_Component_Plugin_Password_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_Login, Kwf_Component_Plugin_Interface_ViewReplace, Kwf_Component_Plugin_Interface_SkipProcessInput
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['password'] = 'planet';
        $ret['generators']['loginForm'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Plugin_Password_LoginForm_Component'
        );
        return $ret;
    }

    protected function _getPassword()
    {
        return Kwc_Abstract::getSetting(get_class($this), 'password');
    }

    protected function _getLoginPassword()
    {
        return isset($_POST['login_password']) ? $_POST['login_password'] : null;
    }

    protected function _saveCookie()
    {
        setcookie(get_class($this), sha1($this->_getLoginPassword()), time()+60*60*24*365);
    }

    /**
     * @deprecated
     */
    public final function isLoggedId() { return $this->isLoggedIn(); }

    public function isLoggedIn()
    {
        $pw = $this->_getPassword();
        if (!$pw) return false; //no password defined

        if (!is_array($pw)) $pw = array($pw);

        if (isset($_COOKIE[get_class($this)])) {
            foreach ($pw as $p) {
                if (sha1($p) == $_COOKIE[get_class($this)]) {
                    $this->_saveCookie();
                    return true;
                }
            }
        }

        $msg = '';
        $session = new Zend_Session_Namespace('login_password');
        if (!$session->passwords) $session->passwords = array();
        if (array_intersect($session->passwords, $pw)) {
            return true;
        }
        if (in_array($this->_getLoginPassword(), $pw)) {
            //this should not happen in herer (we are in isLoggedIn)
            //instead this should be in processInput of the LoginForm, just as Plugin_Login does it
            $session->passwords[] = $this->_getLoginPassword();
            $this->_afterLogin($session);
            $currentPageUrl = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId)->url;
            if ($_SERVER['QUERY_STRING'] && isset($_SERVER['QUERY_STRING'])) {
                $currentPageUrl .= '?'.$_SERVER['QUERY_STRING'];
            }
            if (isset($_POST['save_cookie']) && $_POST['save_cookie']) {
                $this->_saveCookie();
            }
            header('Location: '.$currentPageUrl);
            die();
        }
        return false;
    }

    /**
     * Override to add custom functionality after login
     */
    protected function _afterLogin(Zend_Session_Namespace $session)
    {
    }

    public function getTemplateVars()
    {
        $templateVars = array();
        $templateVars['loginForm'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))->getChildComponent('-loginForm');
        $templateVars['wrongLogin'] = isset($_POST['login_password']);
        $templateVars['placeholder'] = Kwc_Abstract::getSetting(get_class($this), 'placeholder');
        return $templateVars;
    }

    public function replaceOutput()
    {
        if ($this->isLoggedIn()) {
            return false;
        }

        $template = Kwc_Admin::getComponentFile($this, 'Component', 'tpl');

        $renderer = new Kwf_Component_Renderer();
        $view = new Kwf_Component_View($renderer);
        $view->assign($this->getTemplateVars());
        return $renderer->render($view->render($template));
    }

    public function skipProcessInput()
    {
        //!$this->isLoggedIn() would be correct here, but that makes a redirect on login which we don't want
        $session = new Zend_Session_Namespace('login_password');
        return (bool)$session->login;
    }
}
