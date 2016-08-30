<?php
class Kwc_Events_Year_Directory_Component extends Kwc_News_Year_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['model'] = 'Kwc_Events_Directory_Model';
        $ret['dateColumn'] = 'start_date';
        return $ret;
    }
}
