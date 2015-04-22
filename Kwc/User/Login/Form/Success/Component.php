<?php
class Kwc_User_Login_Form_Success_Component extends Kwc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $ret['redirectToUrl'] = $this->getData()
            ->getParentByClass('Kwc_User_Login_Component')
            ->getComponent()
            ->getUrlForRedirect($_REQUEST, $user);
        return $ret;
    }
}
