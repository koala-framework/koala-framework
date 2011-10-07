<?php
class Vpc_Composite_ImagesEnlarge_TestComponent extends Vpc_Composite_ImagesEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Composite_ImagesEnlarge_TestModel';
        $ret['ownModel'] = new Vps_Model_FnF();
        $ret['generators']['child']['component'] = 'Vpc_Composite_ImagesEnlarge_ImageEnlarge_TestComponent';
        return $ret;
    }
}
