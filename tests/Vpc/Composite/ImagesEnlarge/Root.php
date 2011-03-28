<?php
class Vpc_Composite_ImagesEnlarge_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Composite_ImagesEnlarge_PageTestModel';
        $ret['generators']['page']['component'] = array(
            'images' => 'Vpc_Composite_ImagesEnlarge_TestComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
