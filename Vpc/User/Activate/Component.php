<?php
class Vpc_User_Activate_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Activate_Formular_Component';
        return $ret;
    }

}
