<?php
class Vpc_Posts_Detail_Report_Success_Component extends Vpc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVpsStatic('Comment was successfully reported.');
        return $ret;
    }
}
