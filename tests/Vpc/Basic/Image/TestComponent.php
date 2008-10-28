<?php
class Vpc_Basic_Image_TestComponent extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Image_TestModel';
        $ret['dimensions'] = array();
        return $ret;
    }
}
