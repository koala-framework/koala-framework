<?php
class Kwc_Directories_AjaxView_View_Component extends Kwc_Directories_List_ViewPageAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['searchForm'] = 'Kwc_Directories_AjaxView_View_SearchForm_Component';
        return $ret;
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->order('id');
        return $ret;
    }
}
