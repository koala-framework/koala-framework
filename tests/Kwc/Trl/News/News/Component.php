<?php
class Kwc_Trl_News_News_Component extends Kwc_News_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Trl_News_News_TestModel';
        $ret['generators']['detail']['component'] = 'Kwc_Trl_News_News_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Trl_News_News_View_Component';
        return $ret;
    }
}
