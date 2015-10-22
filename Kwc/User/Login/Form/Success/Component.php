<?php
class Kwc_User_Login_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $ret['redirectToUrl'] = $this->getData()
            ->getParentByClass(array('Kwc_User_Login_Component', 'Kwc_User_Login_Trl_Component'))
            ->getComponent()
            ->getUrlForRedirect($_REQUEST, $user);
        return $ret;
    }
}
