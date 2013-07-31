<?php
class RedMallee_FulltextSearch_Search_Directory_Component extends Kwc_FulltextSearch_Search_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'RedMallee_FulltextSearch_Search_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'RedMallee_FulltextSearch_Search_View_Component';
        return $ret;
    }
}
