<?php
class Kwc_Columns_Abstract_ExtConfig extends Kwc_Abstract_List_ExtConfigList
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['list']['xtype'] = 'kwc.columns.list';
        $ret['list']['controllerUrl'] = $this->getControllerUrl();
        $ret['form'] = $this->_getStandardConfig('kwf.autoform', 'Settings', trlKwf('Settings'), new Kwf_Asset('wrench'));
        return $ret;
    }

    public function getEditAfterCreateConfigKey()
    {
        return 'form';
    }
}
