<?php
class Kwf_Util_ClearCache_Types_ComponentSettings extends Kwf_Util_ClearCache_Types_Abstract
{
    //TODO also clear cache?

    protected function _refreshCache($options)
    {
        Kwf_Component_Settings::resetSettingsCache();
        Kwf_Component_Settings::_getSettingsCached();
    }

    public function getTypeName()
    {
        return 'component';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return false; }
}
