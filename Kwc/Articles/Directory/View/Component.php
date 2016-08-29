<?php
class Kwc_Articles_Directory_View_Component extends Kwc_Directories_List_ViewPageAjax_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        $ret['flags']['usesFulltext'] = true;
        $ret['updateTags'][] = 'fulltext';
        return $ret;
    }

    public function getPartialParams()
    {
        $ret = parent::getPartialParams();
        $ret['disableCache'] = true;
        return $ret;
    }
}
