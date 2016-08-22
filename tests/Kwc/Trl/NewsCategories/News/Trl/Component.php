<?php
class Kwc_Trl_NewsCategories_News_Trl_Component extends Kwc_News_Directory_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['childModel'] = 'Kwc_Trl_NewsCategories_News_Trl_TestModel';
        return $ret;
    }
}
