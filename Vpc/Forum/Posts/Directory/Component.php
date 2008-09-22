<?php
class Vpc_Forum_Posts_Directory_Component extends Vpc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_Posts_Directory_View_Component';
        $ret['generators']['detail']['component'] = 'Vpc_Forum_Posts_Detail_Component';
        $ret['generators']['write']['name'] = trlVps('Reply');
        return $ret;
    }
}
