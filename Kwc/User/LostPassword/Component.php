<?php
class Kwc_User_LostPassword_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['setpass'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_User_LostPassword_SetPassword_Component',
            'name' => trlKwfStatic('Set password')
        );
        $ret['generators']['child']['component']['form'] = 'Kwc_User_LostPassword_Form_Component';
        $ret['cssClass'] = 'webStandard webForm';
        return $ret;
    }
}
