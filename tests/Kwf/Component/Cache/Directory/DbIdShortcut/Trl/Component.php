<?php
class Kwf_Component_Cache_Directory_DbIdShortcut_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwf_Component_Cache_Directory_DbIdShortcut_Trl_Model';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
