<?php
class Kwc_Blog_Directory_ExtConfig extends Kwf_Component_Abstract_ExtConfig_None
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $config = $this->_getStandardConfig('kwf.autoform', 'Settings',
                    trlKwf('Blog Settings'),
                    new Kwf_Asset('wrench_orange'));
        $ret['settings'] = $config;
        return $ret;
    }
}
