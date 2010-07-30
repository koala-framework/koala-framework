<?php
class Vpc_User_LostPassword_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['setpass'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_User_LostPassword_SetPassword_Component',
            'name' => trlVps('Set password')
        );
        $ret['generators']['child']['component']['form'] = 'Vpc_User_LostPassword_Form_Component';
        $ret['cssClass'] = 'webStandard webForm';
        return $ret;
    }
}
