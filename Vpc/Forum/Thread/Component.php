<?php
class Vpc_Forum_Thread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['posts'] = 'Vpc_Forum_Posts_Component';
        return $ret;
    }

}
