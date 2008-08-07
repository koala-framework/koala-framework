<?php
class Vpc_User_Detail_Images_Image_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['editComment'] = true;
        return $ret;
    }

}
