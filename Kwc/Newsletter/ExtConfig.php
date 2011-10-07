<?php
class Kwc_Newsletter_ExtConfig extends Kwc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['items']['idSeparator'] = '_';
        return $ret;
    }
}
