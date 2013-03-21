<?php
class Kwf_Component_Plugin_Interface_UseViewCache_Component extends Kwc_Abstract
    implements Kwf_Component_Plugin_Interface_UseViewCache
{
    public static $useViewCache = true;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array('Kwf_Component_Plugin_Interface_UseViewCache_Component');
        return $ret;
    }

    public function getTemplateVars($renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['time'] = microtime(true);
        return $ret;
    }

    public function useViewCache()
    {
        return Kwf_Component_Plugin_Interface_UseViewCache_Component::$useViewCache;
    }
}
