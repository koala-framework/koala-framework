<?php
class Kwf_Component_Plugin_Interface_UseViewCache_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array('Kwf_Component_Plugin_Interface_UseViewCache_Plugin_Component');
        return $ret;
    }

    public function getTemplateVars($renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['time'] = microtime(true);
        return $ret;
    }
}
