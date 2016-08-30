<?php
class Kwc_ArticlesCategory_Category_Detail_Component extends Kwc_Directories_Category_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['list'] = 'Kwc_ArticlesCategory_Category_Detail_List_Component';
        $ret['flags']['hasComponentLinkModifiers'] = false;
        return $ret;
    }
}
