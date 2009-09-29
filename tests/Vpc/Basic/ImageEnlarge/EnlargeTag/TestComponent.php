<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_TestComponent extends Vpc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_ImageEnlarge_TestModel';
        return $ret;
    }
}
