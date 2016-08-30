<?php
class Kwf_Component_OutputReplacePlugin_TestComponent_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['plugins'] = array('Kwf_Component_OutputReplacePlugin_TestPlugin_Component');
        return $ret;
    }
}
