<?php
class Kwc_User_Detail_Gallery_Component extends Kwc_Basic_Gallery_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['showVisible'] = false;
        return $ret;
    }

}
