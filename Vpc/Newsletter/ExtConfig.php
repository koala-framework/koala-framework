<?php
class Vpc_Newsletter_ExtConfig extends Vpc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['items']['idSeparator'] = '_';
        return $ret;
    }
}
