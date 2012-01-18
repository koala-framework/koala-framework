<?php
class Kwc_Basic_None_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('None');
        return $ret;
    }

    public function hasContent()
    {
        return false;
    }
}
