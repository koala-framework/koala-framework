<?php
class Vps_Component_Acl_AllowedComponents_PagesContainer extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['special'] = 'Vps_Component_Acl_AllowedComponents_Pages';
        return $ret;
    }
}
