<?php
class Vpc_User_EditImages_Component extends Vpc_User_Edit_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_EditImages_Form_Component';
        return $ret;
    }
}
