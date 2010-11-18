<?php
class Vpc_Trl_TextImage_TextImage_Component extends Vpc_TextImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_TextImage_TextImage_TestModel';
        $ret['generators']['child']['component']['text'] = 'Vpc_Trl_TextImage_TextImage_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Vpc_Trl_TextImage_TextImage_ImageEnlarge_TestComponent';
        return $ret;
    }
}
