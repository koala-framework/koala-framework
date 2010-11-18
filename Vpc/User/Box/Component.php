<?php
class Vpc_User_Box_Component extends Vpc_User_BoxWithoutLogin_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['login'] = 'Vpc_User_Box_Login_Component';
        $ret['placeholder']['loginHeadline'] = trlVpsStatic('Login:');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }
}
