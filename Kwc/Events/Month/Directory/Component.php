<?php
class Kwc_Events_Month_Directory_Component extends Kwc_News_Month_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['model'] = 'Kwc_Events_Directory_Model';
        $ret['dateColumn'] = 'start_date';
        return $ret;
    }
}
