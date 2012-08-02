<?php
class Kwc_Directories_AjaxView_View_Component extends Kwc_Directories_List_ViewPageAjax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->order('id');
        return $ret;
    }
}
