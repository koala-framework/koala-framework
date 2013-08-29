<?php
class Kwf_Component_Plugin_Password_Component extends Kwf_Component_Plugin_LoginAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['password'] = 'planet';
        $ret['generators']['loginForm']['component'] = 'Kwf_Component_Plugin_Password_LoginForm_Component';
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

        $session = new Kwf_Session_Namespace('login_password');
        if (!$session->passwords) $session->passwords = array();
        if (array_intersect($session->passwords, $pw)) {
            return true;
        }
        if (!is_null($this->_getLoginPassword()) && in_array($this->_getLoginPassword(), $pw)) {
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
            Kwf_Util_Redirect::redirect($currentPageUrl);
        }
        return false;
    }

    /**
     * Override to add custom functionality after login
     */
    protected function _afterLogin(Kwf_Session_Namespace $session)
    {
    }

    public function skipProcessInput()
    {
        // overwrite because parent call that makes a redirect on login which we don't want
        $session = new Kwf_Session_Namespace('login_password');
        return (bool)$session->login;
    }
}
