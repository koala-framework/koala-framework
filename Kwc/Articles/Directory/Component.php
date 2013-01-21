<?php
class Kwc_Articles_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Kwc_Articles_Directory_View_Component';
        return $ret;
    }
}
