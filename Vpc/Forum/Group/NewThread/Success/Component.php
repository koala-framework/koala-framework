<?php
class Vpc_Forum_Group_NewThread_Success_Component extends Vpc_Posts_Success_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['success'] = trlVps('Thread was successfully saved.');
        return $ret;
    }
}
