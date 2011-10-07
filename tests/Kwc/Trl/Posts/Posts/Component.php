<?php
class Kwc_Trl_Posts_Posts_Component extends Kwc_Posts_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Trl_Posts_Posts_Model';
        return $ret;
    }
}
