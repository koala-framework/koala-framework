<?php
class Kwc_List_Gallery_Trl_SettingsExtConfig extends Kwc_Abstract_List_Trl_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['settings'] = $this->_getStandardConfig('kwf.autoform', 'Settings', trlKwf('Settings'), new Kwf_Asset('wrench'));
        return $ret;
    }
}
