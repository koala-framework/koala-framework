<?php
class Vpc_Directories_Item_Directory_ExtConfigTabs extends Vpc_Directories_Item_Directory_ExtConfigAbstract
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['items']['needsComponentPanel'] = false;
        $ret['items']['xtype'] = 'vpc.directories.item.directory.tabs';
        $ret['items']['detailsControllerUrl'] = $this->getControllerUrl('Form');
        $ret['items']['width'] = '500';
        return $ret;
    }
}