<?php
class Vpc_User_Register_Component extends Vpc_Form_Component
{
    protected $_formName = 'Vpc_User_Register_Form';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Login_Form_Component';
        return $ret;
    }

}
