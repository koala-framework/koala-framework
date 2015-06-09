<?php
class Kwc_ListChildPages_Teaser_TeaserWithChild_Component extends Kwc_List_ChildPages_Teaser_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_ListChildPages_Teaser_TeaserWithChild_Child_Component';
        $ret['childModel'] = 'Kwc_ListChildPages_Teaser_TestModel';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }
}
