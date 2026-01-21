<?php
class Kwc_Box_Title_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }
}
