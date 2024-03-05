<?php
class Kwc_FulltextSearch_Search_ViewAjax_SearchForm_Component extends Kwc_Form_NonAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['method'] = 'get';
        $ret['generators']['child']['component']['success'] = false;
        $ret['rootElementClass'] = 'unResponsive';
        return $ret;
    }
}
