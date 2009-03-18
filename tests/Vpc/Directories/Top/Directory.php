<?php
class Vpc_Directories_Top_Directory extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Directories_Top_Model';
        $ret['generators']['detail']['nameColumn'] = 'name';
        return $ret;
    }
}
