<?php
class Vpc_Events_Detail_Component extends Vpc_News_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsAdmin']['dep'][] = 'VpsFormDateTimeField';
        return $ret;
    }
}
