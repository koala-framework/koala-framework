<?php
class Kwc_Basic_Text_TestComponent extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Text_TestModel';
        $ret['stylesModel'] = 'Kwc_Basic_Text_TestStylesModel';
        $ret['generators']['child']['component']['link'] = 'Kwc_Basic_Text_Link_TestComponent';
        $ret['generators']['child']['component']['image'] = 'Kwc_Basic_Text_Image_TestComponent';
        $ret['generators']['child']['component']['download'] = 'Kwc_Basic_Text_Download_TestComponent';
        return $ret;
    }
}
