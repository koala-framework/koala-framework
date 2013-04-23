<?php
class TestTheme_FulltextSearch_Search_Directory_Component extends Kwc_FulltextSearch_Search_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail']['component'] = 'TestTheme_FulltextSearch_Search_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'TestTheme_FulltextSearch_Search_View_Component';
        return $ret;
    }
}
