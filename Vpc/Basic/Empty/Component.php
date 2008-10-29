<?php
class Vpc_Basic_Empty_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Empty');
        return $ret;
    }
    public function hasContent()
    {
        return false;
    }
}
