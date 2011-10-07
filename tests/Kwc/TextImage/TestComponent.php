<?php
class Vpc_TextImage_TestComponent extends Vpc_TextImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_TextImage_TestModel';
        $ret['generators']['child']['component']['text'] = 'Vpc_TextImage_Text_TestComponent';
        $ret['generators']['child']['component']['image'] = 'Vpc_TextImage_ImageEnlarge_TestComponent';
        return $ret;
    }
}
