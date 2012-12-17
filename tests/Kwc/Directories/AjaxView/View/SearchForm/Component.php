<?php
class Kwc_Directories_AjaxView_View_SearchForm_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useAjaxRequest'] = false;
        $ret['method'] = 'get';
        $ret['generators']['child']['component']['success'] = false;
        return $ret;
    }
}
