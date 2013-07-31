<?php
class Kwf_Component_Plugin_LoginRedirect_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace, Kwf_Component_Plugin_Interface_Login, Kwf_Component_Plugin_Interface_SkipProcessInput
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['validUserRoles'] = null;
        return $ret;
    }

    public function replaceOutput($renderer)
    {
        if (!$this->isLoggedIn()) {
            $url = $this->_getRedirectUrl();
            $component = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->_componentId);
            if ($component->url != '/') {
                $connector = '?';
                if (strstr($url, '?')) {
                    $connector = '&';
                }
                $url .= $connector.'redirect=' . urlencode($component->url);
            }
            header('Location: ' . $url);
            exit;
        }
        return false;
    }

    protected function _getRedirectUrl()
    {
        $loginComponent = $this->getLoginComponent();
        if (!$loginComponent) throw new Kwf_Exception('No login component found');
        return $loginComponent->url;
    }

    public function getLoginComponent()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId);
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(array('Kwc_User_Login_Component', 'Kwc_User_Login_Trl_Component'), array('subroot'=>$c));
        return $component;
    }

    public function isLoggedIn()
    {
        if (Kwf_Setup::hasAuthedUser()) {
            $user = Zend_Registry::get('userModel')->getAuthedUser();
            if (!$user) return false;
            if (!$this->_getSetting('validUserRoles')) return true;
            if (in_array($user->role, $this->_getSetting('validUserRoles'))) {
                return true;
            }
        }
        return false;
    }

    public function skipProcessInput()
    {
        return !$this->isLoggedIn();
    }
}
