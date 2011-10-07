<?php
class Kwc_Guestbook_ExtConfig extends Kwc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $config = $this->_getStandardConfig('kwf.autoform', 'Settings',
                    trlKwf('Guestbook Settings'),
                    new Kwf_Asset('wrench_orange'));
        $ret['settings'] = $config;
        $ret['items']['controllerUrl'] = Kwc_Admin::getInstance($this->_class)->getControllerUrl('Comments');
        return $ret;
    }
}
