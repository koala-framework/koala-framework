<?php
class Vpc_TextImage_Text_TestComponent extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_TextImage_Text_TestModel';
        $ret['generators']['child']['model'] = 'Vpc_TextImage_Text_ChildComponentsModel';
        return $ret;
    }

}
