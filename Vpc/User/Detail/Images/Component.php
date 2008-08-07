<?php
class Vpc_User_Detail_Images_Component extends Vpc_Composite_Images_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['showVisible'] = false;
        $ret['generators']['child']['component'] = 'Vpc_User_Detail_Images_Image_Component';
        return $ret;
    }

}
