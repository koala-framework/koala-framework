<?php
class Kwc_Basic_Empty_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Empty');
        return $ret;
    }
    public function hasContent()
    {
        return false;
    }
}
