<?php
class Kwc_User_DeleteAccount_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] = 'Kwc_User_DeleteAccount_Form_Component';
        return $ret;
    }
}
