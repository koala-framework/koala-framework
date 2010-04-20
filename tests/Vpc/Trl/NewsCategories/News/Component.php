<?php
class Vpc_Trl_NewsCategories_News_Component extends Vpc_NewsCategory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Trl_NewsCategories_News_TestModel';
        $ret['generators']['detail']['component'] = 'Vpc_Trl_NewsCategories_News_Detail_Component';
        $ret['generators']['categories']['component'] = 'Vpc_Trl_NewsCategories_News_Category_Component';
        return $ret;
    }
}
