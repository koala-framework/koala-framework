<?php
class Kwc_User_Detail_Gallery_Component extends Kwc_Composite_ImagesEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Gallery');
        $ret['showVisible'] = false;
        return $ret;
    }

}
