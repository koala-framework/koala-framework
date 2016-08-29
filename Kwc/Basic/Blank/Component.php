<?php
class Kwc_Basic_Blank_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Blank');
        return $ret;
    }

    public function hasContent()
    {
        return true;
    }
}
