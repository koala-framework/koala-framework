<?php
class Kwc_Posts_Detail_Report_Success_Component extends Kwc_Posts_Success_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['success'] = trlKwfStatic('Comment was successfully reported.');
        return $ret;
    }
}
