<?php
class Kwc_Basic_LinkTag_Empty_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('None');
        $ret['apiContent'] = 'Kwc_Basic_LinkTag_Empty_ApiContent';
        $ret['apiContentType'] = 'noAction';
        return $ret;
    }

    public function hasContent()
    {
        return false;
    }
}
