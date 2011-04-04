<?php
class Vpc_Composite_Images_TestComponent extends Vpc_Composite_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Composite_Images_TestModel';
        $ret['ownModel'] = 'Vpc_Composite_Images_TestOwnModel';
        $ret['generators']['child']['component'] = 'Vpc_Composite_Images_Image_TestComponent';
        return $ret;
    }
}
