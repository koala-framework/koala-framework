<?php
class Kwf_Component_Cache_Directory_DbIdShortcut_Component extends Kwc_Directories_Item_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['dbIdShortcut'] = 'foo_';
        $ret['childModel'] = 'Kwf_Component_Cache_Directory_DbIdShortcut_Model';
        $ret['generators']['child']['component']['view'] = 'Kwf_Component_Cache_Directory_DbIdShortcut_View_Component';
        $ret['generators']['detail']['class'] = 'Kwf_Component_Cache_Directory_DbIdShortcut_Generator';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }
}
