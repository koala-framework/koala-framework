<?php
class Vps_Component_PagesController_PagesGeneratorActions_SpecialWithoutEditComponent extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}
