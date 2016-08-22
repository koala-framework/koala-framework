<?php
class Kwc_ArticlesCategory_Category_Detail_List_Component extends Kwc_Directories_Category_Detail_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_ArticlesCategory_Directory_View_Component';
        return $ret;
    }
}
