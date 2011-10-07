<?php
class Kwc_User_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['class'] = 'Kwc_User_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_User_Detail_Component';
        $ret['generators']['detail']['model'] = Kwf_Registry::get('config')->user->model;
        $ret['generators']['detail']['dbIdShortcut'] = 'users_';
        $ret['generators']['detail']['filenameColumn'] = 'nickname';
        $ret['generators']['detail']['nameColumn'] = 'nickname';
        $ret['generators']['register'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_Register_Component',
            'name' => trlKwf('Register')
        );
        $ret['generators']['edit'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_Edit_Component',
            'name' => trlKwf('Edit Profile')
        );
        $ret['generators']['login'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_Login_Component',
            'name' => trlKwf('Login')
        );
        $ret['generators']['lostPassword'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_LostPassword_Component',
            'name' => trlKwf('Lost Password')
        );
        $ret['generators']['activate'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_Activate_Component',
            'name' => trlKwf('Activate')
        );
        return $ret;
    }
}
