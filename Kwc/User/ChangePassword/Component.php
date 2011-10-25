<?php
class Kwc_User_ChangePassword_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Edit_Form_Component';
        return $ret;
    }
}
