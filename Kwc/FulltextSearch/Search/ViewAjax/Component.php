<?php
class Kwc_FulltextSearch_Search_ViewAjax_Component extends Kwc_Directories_List_ViewAjax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['searchForm'] = 'Kwc_FulltextSearch_Search_ViewAjax_SearchForm_Component';
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        return $ret;
    }
}
