<?php
class Kwc_Guestbook_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $ret = array();
        $config = $this->_getStandardConfig('kwf.autoform', 'Settings',
                    trlKwf('Guestbook Settings'),
                    new Kwf_Asset('wrench_orange'));
        $ret['settings'] = $config;
        return $ret;
    }
}
