<?php
class Vps_Component_Plugin_Password_Component extends Vps_Component_Plugin_View_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['password'] = 'planet';
        $ret['generators']['loginForm'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vps_Component_Plugin_Password_LoginForm_Component'
        );
        return $ret;
    }

    protected function _getPassword()
    {
        return $this->_getSetting('password');
    }

    public function isLoggedId()
    {
        $pw = $this->_getPassword();
        if (!is_array($pw)) $pw = array($pw);
        $msg = '';
        $session = new Zend_Session_Namespace('login_password');
        if (isset($_POST['login_password'])) {
            if (in_array($_POST['login_password'], $pw)) {
                $session->login = true;
            }
        }
        return $session->login;
    }

    public function processOutput($output)
    {
        if ($this->isLoggedId()) return $output;

        $templateVars = array();
        $templateVars['loginForm'] = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))->getChildComponent('-loginForm');
        $templateVars['wrongLogin'] = isset($_POST['login_password']);
        $templateVars['placeholder'] = $this->_getSetting('placeholder');

        $template = Vpc_Admin::getComponentFile($this, 'Component', 'tpl');
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }
}
