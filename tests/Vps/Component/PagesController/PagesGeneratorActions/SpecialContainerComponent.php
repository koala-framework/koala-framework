<?php
class Vps_Component_PagesController_PagesGeneratorActions_SpecialContainerComponent extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['special'] = 'Vps_Component_PagesController_PagesGeneratorActions_SpecialComponent';
        return $ret;
    }
}
