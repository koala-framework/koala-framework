<?php
class Vpc_User_Edit_Component extends Vpc_Abstract_Composite_Component 
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_User_Edit_Form_Component';
        $ret['forms'] = 'all';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
