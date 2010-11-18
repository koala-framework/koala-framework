<?php
class Vpc_Trl_TextImage_TextImage_ImageEnlarge_TestComponent extends Vpc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_TextImage_TextImage_ImageEnlarge_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestComponent';
        return $ret;
    }

}
