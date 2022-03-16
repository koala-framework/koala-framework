<?php
abstract class Kwc_FulltextSearch_Search_ViewAjax_AbstractComponent extends Kwc_Directories_List_ViewAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        return $ret;
    }

    protected function _getSearchForm()
    {
        return $this->getData()->getParentComponent()->getChildComponent('-searchForm');
    }
}
