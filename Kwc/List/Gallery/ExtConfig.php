<?php
class Kwc_List_Gallery_ExtConfig extends Kwc_Abstract_List_ExtConfigListUpload
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['settings'] = $this->_getStandardConfig('kwf.autoform', 'Settings', trlKwf('Settings'), new Kwf_Asset('wrench'));
        return $ret;
    }

    public function getEditAfterCreateConfigKey()
    {
        return 'settings';
    }
}
