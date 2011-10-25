<?php
class Kwc_User_Box_Component extends Kwc_User_BoxWithoutLogin_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['login'] = 'Kwc_User_Box_Login_Component';
        $ret['placeholder']['loginHeadline'] = trlKwfStatic('Login:');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }
}
