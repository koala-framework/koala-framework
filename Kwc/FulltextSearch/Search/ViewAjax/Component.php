<?php
/**
 * @deprecated
 */
class Kwc_FulltextSearch_Search_ViewAjax_Component extends Kwc_FulltextSearch_Search_ViewAjax_AbstractComponent
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['searchForm'] = 'Kwc_FulltextSearch_Search_ViewAjax_SearchForm_Component';
        return $ret;
    }
}
