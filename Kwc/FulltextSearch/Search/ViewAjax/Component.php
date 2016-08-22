<?php
class Kwc_FulltextSearch_Search_ViewAjax_Component extends Kwc_Directories_List_ViewAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['searchForm'] = 'Kwc_FulltextSearch_Search_ViewAjax_SearchForm_Component';
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        return $ret;
    }
}
