<?php
class Kwc_User_BoxWithoutLogin_Box_Component extends Kwc_User_BoxWithoutLogin_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['showLostPassword'] = false;
        $ret['showLoginLink'] = false;
        $ret['showRegisterLink'] = false;
        $ret['generators']['child']['component']['loggedIn'] = 'Kwc_User_BoxWithoutLogin_Box_LoggedIn_Component';
        return $ret;
    }
}
