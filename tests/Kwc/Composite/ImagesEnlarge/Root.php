<?php
class Kwc_Composite_ImagesEnlarge_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Composite_ImagesEnlarge_PageTestModel';
        $ret['generators']['page']['component'] = array(
            'images' => 'Kwc_Composite_ImagesEnlarge_TestComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
