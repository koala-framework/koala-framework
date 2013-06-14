<?php
class Kwc_IncludeCode_Page2_Child_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }
}
