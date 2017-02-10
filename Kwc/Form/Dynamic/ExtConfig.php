<?php
class Kwc_Form_Dynamic_ExtConfig extends Kwc_Abstract_Composite_ExtConfigTabs
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['tabs']['tabs']['ownForm'] = $this->_getStandardConfig('kwf.autoform');
        return $ret;
    }
}
