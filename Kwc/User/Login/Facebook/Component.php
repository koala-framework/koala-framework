<?php
class Kwc_User_Login_Facebook_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'KwfFacebook';
        $ret['assets']['files'][] = 'kwf/Kwc/User/Login/Facebook/Component.js';
        $ret['generators']['child']['component']['success'] = 'Kwc_User_Login_Form_Success_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['config']['controllerUrl'] = Kwc_Admin::getInstance($this
            ->getData()->componentClass)->getControllerUrl('Component');
        return $ret;
    }
}
