<?php
class Kwc_Menu_EditableItems_ExtConfig extends Kwc_Abstract_List_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['listWidth'] = 120;
        return $ret;
    }
}
