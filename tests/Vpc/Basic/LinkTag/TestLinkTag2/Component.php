<?php
class Vpc_Basic_LinkTag_TestLinkTag2_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_TestLinkTag2_Data';
        return $ret;
    }
}
