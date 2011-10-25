<?php
class Kwc_NewsCategory_View_Component extends Kwc_News_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = 'Kwc_NewsCategory_View_Paging_Component';
        return $ret;
    }
}
