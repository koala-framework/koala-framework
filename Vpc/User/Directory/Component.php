<?php
class Vpc_User_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Vpc_User_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_User_Detail_Component';
        $ret['generators']['detail']['model'] = Vps_Registry::get('config')->user->model;
        $ret['generators']['detail']['dbIdShortcut'] = 'users_';
        $ret['generators']['detail']['filenameColumn'] = 'nickname';
        $ret['generators']['detail']['nameColumn'] = 'nickname';
        $ret['generators']['register'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_Register_Component',
            'name' => trlVps('Register')
        );
        $ret['generators']['edit'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_Edit_Component',
            'name' => trlVps('Edit Profile')
        );
        $ret['generators']['login'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_Login_Component',
            'name' => trlVps('Login')
        );
        $ret['generators']['lostPassword'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_LostPassword_Component',
            'name' => trlVps('Lost Password')
        );
        $ret['generators']['activate'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_Activate_Component',
            'name' => trlVps('Activate')
        );
        return $ret;
    }
}
