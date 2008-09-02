<?php
class Vpc_Forum_Posts_Directory_Component extends Vpc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Forum_Posts_Directory_View_Component';
        return $ret;
    }
}
