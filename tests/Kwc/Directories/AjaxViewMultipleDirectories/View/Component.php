<?php
class Kwc_Directories_AjaxViewMultipleDirectories_View_Component extends Kwc_Directories_List_ViewPageAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        return $ret;
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->order('id');
        return $ret;
    }
}
