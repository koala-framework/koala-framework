<?php
class Kwf_Component_PagesController_PagesGeneratorActions_SpecialWithoutEditContainerComponent extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['special'] = 'Kwf_Component_PagesController_PagesGeneratorActions_SpecialWithoutEditComponent';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['editComponents'] = array('special');
        return $ret;
    }
}
