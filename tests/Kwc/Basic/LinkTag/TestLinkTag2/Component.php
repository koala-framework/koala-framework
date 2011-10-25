<?php
class Kwc_Basic_LinkTag_TestLinkTag2_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_TestLinkTag2_Data';
        return $ret;
    }
}
