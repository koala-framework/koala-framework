<?php
class Kwf_Component_OutputReplacePlugin_TestComponent_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array('Kwf_Component_OutputReplacePlugin_TestPlugin_Component');
        return $ret;
    }
}
