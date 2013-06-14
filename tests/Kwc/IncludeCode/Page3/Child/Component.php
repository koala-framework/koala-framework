<?php
class Kwc_IncludeCode_Page3_Child_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return '<meta name="test" content="foo" >';
    }
}
