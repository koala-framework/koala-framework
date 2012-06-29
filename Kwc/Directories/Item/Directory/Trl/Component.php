<?php
class Kwc_Directories_Item_Directory_Trl_Component extends Kwc_Directories_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['detail']['class'] = 'Kwc_Directories_Item_Directory_Trl_Generator';
        $ret['extConfig'] = 'Kwc_Directories_Item_Directory_Trl_ExtConfigEditButtons';
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return array($directoryClass);
    }

    public static function getCacheMetaForView($view)
    {
        return Kwc_Directories_Item_Directory_Cc_Component::getCacheMetaForView($view);
    }
}
