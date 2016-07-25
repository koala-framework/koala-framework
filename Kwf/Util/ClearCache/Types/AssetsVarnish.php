<?php
class Kwf_Util_ClearCache_Types_AssetsVarnish extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Util_Varnish::purge('/assets/*');
    }

    public function getTypeName()
    {
        return 'assets';
    }

    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
