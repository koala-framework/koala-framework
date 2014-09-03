<?php
class Kwc_Basic_TextSessionModel_TestComponent extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_TextSessionModel_TestModel';
        $ret['stylesModel'] = 'Kwc_Basic_TextSessionModel_TestStylesModel';
        $ret['generators']['child']['component']['link'] = 'Kwc_Basic_TextSessionModel_Link_Component';
        $ret['generators']['child']['component']['image'] = false;
        $ret['generators']['child']['component']['download'] = false;
        return $ret;
    }
}
