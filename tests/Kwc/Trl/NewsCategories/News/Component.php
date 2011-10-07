<?php
class Kwc_Trl_NewsCategories_News_Component extends Kwc_NewsCategory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Trl_NewsCategories_News_TestModel';
        $ret['generators']['detail']['component'] = 'Kwc_Trl_NewsCategories_News_Detail_Component';
        $ret['generators']['categories']['component'] = 'Kwc_Trl_NewsCategories_News_Category_Component';
        return $ret;
    }
}
