<?php
class Vpc_Basic_ImageEnlarge_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Vpc_Basic_ImageEnlarge_PageTestModel';
        $ret['generators']['page']['component'] = array(
            'image' => 'Vpc_Basic_ImageEnlarge_TestComponent',
            'imageWithoutSmall' => 'Vpc_Basic_ImageEnlarge_WithoutSmallImageComponent',
            'imageWithOriginal' => 'Vpc_Basic_ImageEnlarge_OriginalImageComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
