<?php
class Vpc_Shop_Products_Directory_ExtConfig extends Vpc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['items']['idTemplate'] = 'shopProducts_{0}-content';
        return $ret;
    }
}
