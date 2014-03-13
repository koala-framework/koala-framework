<?php
class Kwc_Menu_Expanded_EditableItems_ExtConfig extends Kwc_Abstract_List_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['listWidth'] = 170;
        return $ret;
    }
}
