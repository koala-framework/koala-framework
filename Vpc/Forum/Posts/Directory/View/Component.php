<?php
class Vpc_Forum_Posts_Directory_View_Component extends Vpc_Posts_Directory_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['observe'] = 'Vpc_Forum_Posts_Observe_Component';
        return $ret;
    }
}
