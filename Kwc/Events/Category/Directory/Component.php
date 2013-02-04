<?php
class Kwc_Events_Category_Directory_Component extends Kwc_NewsCategory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['categoryToItemModelName'] = 'Kwc_Events_Category_Directory_EventsToCategoriesModel';
        return $ret;
    }
}
