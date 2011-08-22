<?php
class Vpc_Guestbook_ExtConfig extends Vpc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $config = $this->_getStandardConfig('vps.autoform', 'Settings',
                    trlVps('Guestbook Settings'),
                    new Vps_Asset('wrench_orange'));
        $ret['settings'] = $config;
        return $ret;
    }
}
