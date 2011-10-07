<?php
class Kwc_Posts_Detail_Report_Success_Component extends Kwc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlKwfStatic('Comment was successfully reported.');
        return $ret;
    }
}
