<?php
class Vpc_Posts_Post_Report_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['reportMail'] = 'content@vivid-planet.com';
        $ret['reportMailName'] = '';
        return $ret;
    }
}
