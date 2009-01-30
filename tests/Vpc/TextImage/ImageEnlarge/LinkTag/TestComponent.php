<?php
class Vpc_TextImage_ImageEnlarge_LinkTag_TestComponent extends Vpc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_TextImage_ImageEnlarge_LinkTag_TestModel';
        $ret['generators']['link']['component']['enlarge'] = 'Vpc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent';
        return $ret;
    }
}
