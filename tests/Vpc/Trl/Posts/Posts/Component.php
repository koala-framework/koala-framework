<?php
class Vpc_Trl_Posts_Posts_Component extends Vpc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Trl_Posts_Posts_Model';
        return $ret;
    }
}
