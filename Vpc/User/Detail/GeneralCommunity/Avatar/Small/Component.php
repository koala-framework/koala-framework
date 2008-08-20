<?php
class Vpc_User_Detail_GeneralCommunity_Avatar_Small_Component extends Vpc_Basic_Image_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['type'] = 'small';
        return $ret;
    }
}
