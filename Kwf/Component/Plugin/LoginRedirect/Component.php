<?php
class Kwf_Component_Plugin_LoginRedirect_Component extends Kwf_Component_Plugin_View_Abstract
    implements Kwf_Component_Plugin_Interface_Login
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['validUserRoles'] = null;
        return $ret;
    }

    public function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_BEFORE;
    }

    public function processOutput($output)
    {
        if (!$this->isLoggedIn()) {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId);
            $loginComponent = $this->getLoginComponent();
            if (!$loginComponent) throw new KWf_Exception('No login component found');
            $url = $loginComponent->url;
            if ($component->url != '/') {
                $url .= '?redirect=' . urlencode($component->url);
            }
            header('Location: ' . $url);
            exit;
        }
        return parent::processOutput($output);
    }

    public function getLoginComponent()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentByClass('Kwc_User_Login_Component');
        return $component;
    }

    public function isLoggedIn()
    {
        if (!Zend_Session::sessionExists() && !Kwf_Config::getValue('autologin')) return false;
        $user = Zend_Registry::get('userModel')->getAuthedUser();
        if (is_null($user)) return false;
        if (!$this->_getSetting('validUserRoles')) return true;
        if (in_array($user->role, $this->_getSetting('validUserRoles'))) {
            return true;
        }
        return false;
    }
}
