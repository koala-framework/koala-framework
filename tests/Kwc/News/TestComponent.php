<?php
class Vpc_News_TestComponent extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_News_TestChildModel';
        unset($ret['generators']['feed']);
        return $ret;
    }
}
