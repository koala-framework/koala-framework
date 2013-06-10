<?php
class CatchBox_FulltextSearch_Search_Directory_Component extends Kwc_FulltextSearch_Search_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'CatchBox_FulltextSearch_Search_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'CatchBox_FulltextSearch_Search_View_Component';
        return $ret;
    }
}
