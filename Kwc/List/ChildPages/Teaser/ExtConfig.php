<?php
class Kwc_List_ChildPages_Teaser_ExtConfig extends Kwc_Abstract_List_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['xtype'] = 'kwc.List.childPages.teaser';
        return $ret;
    }
}
