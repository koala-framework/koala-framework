<?php
class Vpc_Basic_Image_Enlarge_Title_Component extends Vpc_Basic_Image_Enlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['editComment'] = true;
        $ret['hasSmallImageComponent'] = false;
        return $ret;
    }
}