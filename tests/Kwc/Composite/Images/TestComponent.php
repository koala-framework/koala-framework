<?php
class Kwc_Composite_Images_TestComponent extends Kwc_List_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Composite_Images_TestModel';
        $ret['ownModel'] = 'Kwc_Composite_Images_TestOwnModel';
        $ret['generators']['child']['component'] = 'Kwc_Composite_Images_Image_TestComponent';
        return $ret;
    }
}
