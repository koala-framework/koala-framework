<?php
class Kwc_Basic_Html_TestComponent extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Html_TestModel';
        $ret['generators']['child']['component']['test'] = 'Kwc_Basic_Html_ChildComponent_Component';
        return $ret;
    }
}
