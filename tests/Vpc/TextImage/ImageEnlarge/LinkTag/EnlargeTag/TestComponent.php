<?php
class Vpc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent extends Vpc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_TextImage_ImageEnlarge_TestModel';
        
        return $ret;
    }
}
