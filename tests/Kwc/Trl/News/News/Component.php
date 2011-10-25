<?php
class Kwc_Trl_News_News_Component extends Kwc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Trl_News_News_TestModel';
        $ret['generators']['detail']['component'] = 'Kwc_Trl_News_News_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_List_ViewPage_Component';
        return $ret;
    }
}
