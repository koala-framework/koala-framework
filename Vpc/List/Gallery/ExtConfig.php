<?php
class Vpc_List_Gallery_ExtConfig extends Vpc_Abstract_List_ExtConfigListUpload
{
    protected function _getConfig()
    {
        $ret = array(
            'settings' => $this->_getStandardConfig('vps.autoform', 'Settings', trlVps('Settings'))
        );
        return array_merge($ret, parent::_getConfig());
    }
}
