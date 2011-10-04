<?php
class Vpc_Basic_LinkTag_FirstChildPage_Cc_Component extends Vpc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_FirstChildPage_Data';
        return $ret;
    }
}
