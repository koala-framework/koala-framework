<?php
class Kwc_Trl_Posts_Posts_Component extends Kwc_Posts_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Trl_Posts_Posts_Model';
        return $ret;
    }
}
