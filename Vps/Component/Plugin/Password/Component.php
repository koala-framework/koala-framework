<?php
class Vps_Component_Plugin_Password_Component extends Vps_Component_Plugin_Abstract
    implements Vps_Component_Plugin_Interface_View
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['password'] = 'planet';
        $ret['childComponentClasses']['loginForm'] = 'Vps_Component_Plugin_Password_LoginForm_Component';
        return $ret;
    }

    public function processOutput($output)
    {
        $pw = $this->_getSetting('password');
        if (!is_array($pw)) $pw = array($pw);

        $msg = '';
        $session = new Zend_Session_Namespace('password');
        if (isset($_POST['password'])) {
            if (in_array($_POST['password'], $pw)) {
                $session->login = true;
            }
        }
        if ($session->login) return $output;

        $templateVars = array();
        $templateVars['loginForm'] = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId)->getChildComponent('-loginForm');

        $template = Vpc_Admin::getComponentFile($this, 'Component', 'tpl');
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }
}
