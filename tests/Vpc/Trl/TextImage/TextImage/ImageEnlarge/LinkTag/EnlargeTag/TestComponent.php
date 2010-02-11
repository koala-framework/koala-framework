<?php
class Vpc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent extends Vpc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_TextImage_TextImage_ImageEnlarge_TestModel';
        return $ret;
    }
}
