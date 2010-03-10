<?php
class Vpc_NewsCategory_View_Component extends Vpc_News_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = 'Vpc_NewsCategory_View_Paging_Component';
        return $ret;
    }
}
