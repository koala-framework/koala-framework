<?php
class Vpc_Basic_LinkTag_Empty_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('None');
        return $ret;
    }

    public function hasContent()
    {
        return false;
    }
}
