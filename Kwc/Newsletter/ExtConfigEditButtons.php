<?php
class Kwc_Newsletter_ExtConfigEditButtons extends Kwc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $config = parent::_getConfig();
        $config['items']['xtype'] = 'kwc.newsletter';
        return $config;
    }
}
