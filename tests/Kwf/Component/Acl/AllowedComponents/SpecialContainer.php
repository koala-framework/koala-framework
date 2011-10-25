<?php
class Kwf_Component_Acl_AllowedComponents_SpecialContainer extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['special'] = 'Kwf_Component_Acl_AllowedComponents_Special';
        return $ret;
    }
}