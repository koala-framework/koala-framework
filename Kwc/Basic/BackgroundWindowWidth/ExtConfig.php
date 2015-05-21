<?php
class Kwc_Basic_BackgroundWindowWidth_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Form
{
    protected function _getConfig()
    {
        $ret = parent::_getConfig();
        $ret['form']['title'] = trlKwf('Background Settings');
        return $ret;
    }
}

