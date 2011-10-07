<?php
class Vpc_Basic_LinkTag_FirstChildPage_Trl_Component extends Vpc_Basic_LinkTag_Abstract_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_FirstChildPage_Data';
        return $ret;
    }

}
