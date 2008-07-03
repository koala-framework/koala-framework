<?php
class Vpc_Forum_User_View_Images_Image_Component extends Vpc_Basic_Image_Enlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(640, 480);
        $ret['childComponentClasses']['smallImage'] = 'Vpc_Forum_User_View_Images_Image_Small_Component';
        $ret['editComment'] = true;
        return $ret;
    }

}
