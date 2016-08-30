<?php
class Kwc_Trl_FirstChildPage_Category_Trl_Component extends Kwc_Root_Category_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['page']['model'] = 'Kwc_Trl_FirstChildPage_Category_Trl_PagesModel';
        return $ret;
    }
}
