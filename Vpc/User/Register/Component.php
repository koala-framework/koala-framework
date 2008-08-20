<?php
class Vpc_User_Register_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Register_Form_Component';
        $ret['forms'] = array('general');
        return $ret;
    }

}
