<?php
class Vps_Component_PagesController_PagesGeneratorActions_SpecialComponent extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'special';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
}
