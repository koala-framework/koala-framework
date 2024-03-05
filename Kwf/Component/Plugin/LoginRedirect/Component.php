<?php
class Kwf_Component_Plugin_LoginRedirect_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace, Kwf_Component_Plugin_Interface_Login,
               Kwf_Component_Plugin_Interface_SkipProcessInput, Kwf_Component_Plugin_Interface_Redirect
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['validUserRoles'] = null;
        return $ret;
    }

    public function getRedirectUrl(Kwf_Component_Data $page)
    {
        if (!$this->isLoggedIn()) {
            $url = $this->_getRedirectUrl();
            $connector = (strstr($url, '?')) ? '&' : '?';
            $url .= $connector . 'redirect=' . urlencode($this->_getRedirectParam());
            return $url;
        }
        return false;
    }

    public function replaceOutput($renderer)
    {
        if (!$this->isLoggedIn()) {
            throw new Kwf_Exception("Component should not be rendered, redirect did not happen probably because this plugin is not used at page level");
        }
        return false;
    }

    protected function _getRedirectParam()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId);
        return $component->url;
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

    public function skipProcessInput(Kwf_Component_Data $data)
    {
        while ($data->parent && !$data->isPage) {
            if ($data->componentId == $this->_componentId) {
                return !$this->isLoggedIn();
            }
            $data = $data->parent;
        }
        return false;
    }
}
