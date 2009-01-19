<?php
class Vpc_Basic_Html_TestComponent extends Vpc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Html_TestModel';
        $ret['generators']['child']['component']['test'] = 'Vpc_Basic_Html_ChildComponent_Component';
        return $ret;
    }
}
