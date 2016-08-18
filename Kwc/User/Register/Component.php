<?php
class Kwc_User_Register_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Register_Form_Component';
        $ret['forms'] = array('general');
        return $ret;
    }

}
