<?php
class Kwc_Events_Category_Directory_Component extends Kwc_News_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Events_Category_Directory_Model';
        $ret['categoryToItemModelName'] = 'Kwc_Events_Category_Directory_EventsToCategoriesModel';
        return $ret;
    }
}
