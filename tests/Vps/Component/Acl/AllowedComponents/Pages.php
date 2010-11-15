<?php
class Vps_Component_Acl_AllowedComponents_Pages extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Acl_AllowedComponents_PageDetail'
        );
        return $ret;
    }
}