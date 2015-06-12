<?php
class Kwc_Directories_MapView_View_Component extends Kwc_Directories_List_ViewMap_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['mapOptions']['zoom'] = 7;
        $ret['mapOptions']['zoomProperties'] = 1;
        $ret['mapOptions']['height'] = 400;
        $ret['mapOptions']['width'] = '';
        $ret['mapOptions']['scale'] = 1;
        $ret['mapOptions']['satelite'] = 1;
        $ret['mapOptions']['overview'] = 1;
        $ret['mapOptions']['mapType'] = true;
        $ret['generators']['child']['component']['searchForm'] = 'Kwc_Directories_MapView_View_SearchForm_Component';
        return $ret;
    }

    protected function _getSearchSelect($ret, $searchRow)
    {
        $ret->where(new Kwf_Model_Select_Expr_Like('name', '%'.$searchRow->query.'%'));
        return $ret;
    }
}
