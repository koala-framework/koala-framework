<?php
class Kwf_Component_PagesController_PagesGeneratorActions_SpecialWithoutEditComponent extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}
