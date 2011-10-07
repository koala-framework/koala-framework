<?php
class Vpc_Shop_Cart_View_Component extends Vpc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }
}
