<?php
class Kwc_TextImage_TestComponent extends Kwc_TextImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_TextImage_TestModel';
        $ret['generators']['child']['component']['text'] = 'Kwc_TextImage_Text_TestComponent';
        $ret['generators']['child']['component']['image'] = 'Kwc_TextImage_ImageEnlarge_TestComponent';
        return $ret;
    }
}
