<?php
class Vpc_Basic_LinkTagEvent_TestComponent extends Vpc_Basic_LinkTag_Event_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_LinkTagEvent_TestModel';
        return $ret;
    }
}
