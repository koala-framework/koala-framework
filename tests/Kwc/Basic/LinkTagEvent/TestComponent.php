<?php
class Kwc_Basic_LinkTagEvent_TestComponent extends Kwc_Basic_LinkTag_Event_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkTagEvent_TestModel';
        return $ret;
    }
}
