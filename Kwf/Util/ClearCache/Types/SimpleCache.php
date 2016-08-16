<?php
class Kwf_Util_ClearCache_Types_SimpleCache extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Cache_Simple::_clear();
    }

    public function getTypeName()
    {
        return 'simple';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
