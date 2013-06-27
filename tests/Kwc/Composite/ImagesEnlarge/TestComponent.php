<?php
class Kwc_Composite_ImagesEnlarge_TestComponent extends Kwc_List_Gallery_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Composite_ImagesEnlarge_TestModel';
        $ret['ownModel'] = new Kwf_Model_FnF();
        $ret['generators']['child']['component'] = 'Kwc_Composite_ImagesEnlarge_ImageEnlarge_TestComponent';
        return $ret;
    }
}
