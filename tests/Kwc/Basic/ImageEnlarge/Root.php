<?php
class Kwc_Basic_ImageEnlarge_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwc_Basic_ImageEnlarge_PageTestModel';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'image' => 'Kwc_Basic_ImageEnlarge_TestComponent',
            'imageWithoutSmall' => 'Kwc_Basic_ImageEnlarge_WithoutSmallImageComponent',
            'imageWithOriginal' => 'Kwc_Basic_ImageEnlarge_OriginalImageComponent',
        );

        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
