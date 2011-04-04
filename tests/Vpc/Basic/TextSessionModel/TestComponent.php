<?php
class Vpc_Basic_TextSessionModel_TestComponent extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_TextSessionModel_TestModel';
        $ret['stylesModel'] = 'Vpc_Basic_TextSessionModel_TestStylesModel';
        $ret['generators']['child']['model'] = 'Vpc_Basic_TextSessionModel_TestChildComponentsModel';
        $ret['generators']['child']['component']['link'] = 'Vpc_Basic_TextSessionModel_Link_Component';
        $ret['generators']['child']['component']['image'] = false;
        $ret['generators']['child']['component']['download'] = false;
        $ret['assets']['files']['styles'] = 'dynamic/Vpc_Basic_Text_StylesAsset:Vpc_Basic_TextSessionModel_TestStylesModel';
        return $ret;
    }
}
