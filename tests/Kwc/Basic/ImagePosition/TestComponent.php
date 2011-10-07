<?php
class Vpc_Basic_ImagePosition_TestComponent extends Vpc_Basic_ImagePosition_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_ImagePosition_TestModel';
        $ret['generators']['child']['component']['image'] = 'Vpc_Basic_ImagePosition_Image_TestComponent';
        return $ret;
    }
}
