<?php
class Kwc_User_LostPassword_SetPassword_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] =
            'Kwc_User_LostPassword_SetPassword_Form_Component';
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        return $ret;
    }
}
