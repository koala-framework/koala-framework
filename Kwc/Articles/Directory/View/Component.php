<?php
class Kwc_Articles_Directory_View_Component extends Kwc_Directories_List_ViewPageAjax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';
        $ret['flags']['usesFulltext'] = true;
        $ret['updateTags'][] = 'fulltext';
        return $ret;
    }
}
