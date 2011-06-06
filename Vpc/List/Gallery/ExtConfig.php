<?php
class Vpc_List_Gallery_ExtConfig extends Vpc_Abstract_List_ExtConfigListUpload
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['settings'] = $this->_getStandardConfig('vps.autoform', 'Settings', trlVps('Settings'), new Vps_Asset('wrench'));
        return $ret;
    }

    public function getEditAfterCreateConfigKey()
    {
        return 'settings';
    }
}
