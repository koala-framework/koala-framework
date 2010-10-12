<?php
class Vps_Component_PagesController_PagesGeneratorActions_SpecialContainerComponent extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'special container';
        $ret['generators']['child']['component']['special'] = 'Vps_Component_PagesController_PagesGeneratorActions_SpecialComponent';
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
        $ret['editComponents'] = array('special');
        return $ret;
    }
}
