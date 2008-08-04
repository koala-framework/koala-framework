<?php
class Vpc_User_Directory_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'Vpc_User_Detail_Component';
        $ret['generators']['detail']['table'] = get_class(Vps_Registry::get('userModel'));
        $ret['generators']['register'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_Register_Component',
            'name' => trlVps('Register')
        );
        $ret['generators']['edit'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_Edit_Component',
            'name' => trlVps('Edit')
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
