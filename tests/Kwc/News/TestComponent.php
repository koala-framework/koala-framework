<?php
class Kwc_News_TestComponent extends Kwc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_News_TestChildModel';
        unset($ret['generators']['feed']);
        return $ret;
    }
}
