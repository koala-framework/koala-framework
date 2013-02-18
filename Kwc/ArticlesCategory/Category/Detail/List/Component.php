<?php
class Kwc_ArticlesCategory_Category_Detail_List_Component extends Kwc_Directories_Category_Detail_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_ArticlesCategory_Directory_View_Component';
        return $ret;
    }
}
