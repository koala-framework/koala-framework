<?php
class Kwc_Basic_LinkTag_Empty_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('None');
        return $ret;
    }

    public function hasContent()
    {
        return false;
    }
}
