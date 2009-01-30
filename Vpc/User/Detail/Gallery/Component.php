<?php
class Vpc_User_Detail_Gallery_Component extends Vpc_Composite_ImagesEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Gallery');
        $ret['showVisible'] = false;
        return $ret;
    }

}
