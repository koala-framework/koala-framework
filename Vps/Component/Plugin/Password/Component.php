<?php
class Vps_Component_Plugin_Password_Component extends Vps_Component_Plugin_Abstract
    implements Vps_Component_Plugin_Interface_View
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
        $session = new Zend_Session_Namespace('password');
        if (isset($_POST['password'])) {
            if (in_array($_POST['password'], $pw)) {
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
            ->getComponentById($this->_componentId)->getChildComponent('-loginForm');
        $templateVars['wrongLogin'] = isset($_POST['password']);

        $template = Vpc_Admin::getComponentFile($this, 'Component', 'tpl');
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }
}
