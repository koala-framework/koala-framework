<?php
class Vpc_Forum_Posts_Detail_Component extends Vpc_Posts_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['actions'] = 'Vpc_Forum_Posts_Detail_Actions_Component';
        return $ret;
    }
}
