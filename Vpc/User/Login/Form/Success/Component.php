<?php
class Vpc_User_Login_Form_Success_Component extends Vpc_Form_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['redirectTo'] = $_SERVER['REQUEST_URI'];
        $ret['redirectTo'] = preg_replace('/(\?)logout=?[^&]*&?/', '$1', $ret['redirectTo']);
        return $ret;
    }
}
