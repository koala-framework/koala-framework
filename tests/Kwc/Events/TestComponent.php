<?php
class Kwc_Events_TestComponent extends Kwc_Events_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Events_TestChildModel';
        unset($ret['generators']['feed']);
        return $ret;
    }
}
