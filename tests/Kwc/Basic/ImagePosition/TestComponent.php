<?php
class Kwc_Basic_ImagePosition_TestComponent extends Kwc_Basic_ImagePosition_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_ImagePosition_TestModel';
        $ret['generators']['child']['component']['image'] = 'Kwc_Basic_ImagePosition_Image_TestComponent';
        return $ret;
    }
}
