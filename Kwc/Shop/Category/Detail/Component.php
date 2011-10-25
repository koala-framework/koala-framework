<?php
class Kwc_Shop_Category_Detail_Component extends Kwc_Directories_Category_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['list'] = 'Kwc_Shop_Category_Detail_List_Component';
        return $ret;
    }
}
