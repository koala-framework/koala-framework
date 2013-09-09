<?php
class Kwc_FulltextSearch_Search_ViewAjax_SearchForm_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['requestHttps'] = false;
        $ret['useAjaxRequest'] = false;
        $ret['method'] = 'get';
        $ret['generators']['child']['component']['success'] = false;
        $ret['cssClass'] = 'unResponsive';
        return $ret;
    }
}
