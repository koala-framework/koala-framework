<?php
class Kwc_Shop_Cart_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        return $ret;
    }
}
