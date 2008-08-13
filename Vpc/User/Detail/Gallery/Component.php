<?php
class Vpc_User_Detail_Gallery_Component extends Vpc_Composite_ImagesEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Gallery');
        $ret['showVisible'] = false;
        $ret['generators']['child']['component'] = 'Vpc_Basic_Image_Enlarge_Title_Component';
        return $ret;
    }

}
