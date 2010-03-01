<?php
class Vps_Component_Generator_InheritDifferentComponentClass_Box_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['inheritComponentClass'] = 'Vps_Component_Generator_InheritDifferentComponentClass_Box_Inherit_Component';
        return $ret;
    }
}
