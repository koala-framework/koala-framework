<?php
class Kwc_Basic_LinkTag_TestLinkTag_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_TestLinkTag_Data';
        return $ret;
    }
}
