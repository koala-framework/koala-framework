<?php
class Kwc_Basic_LinkTag_FirstChildPage_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_FirstChildPage_Data';
        return $ret;
    }
}
