<?php
class Vpc_Events_Category_Directory_Component extends Vpc_News_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['categoryToItemModelName'] = 'Vpc_Events_Category_Directory_EventsToCategoriesModel';
        return $ret;
    }
}
