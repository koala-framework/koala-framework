<?php
class Vpc_Events_TestComponent extends Vpc_Events_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Events_TestChildModel';
        unset($ret['generators']['feed']);
        return $ret;
    }
}
