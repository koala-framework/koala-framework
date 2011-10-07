<?php
class Vpc_Trl_News_News_Component extends Vpc_News_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Trl_News_News_TestModel';
        $ret['generators']['detail']['component'] = 'Vpc_Trl_News_News_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_List_ViewPage_Component';
        return $ret;
    }
}
