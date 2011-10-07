<?php
class Kwc_User_Activate_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_User_Activate_Form_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
