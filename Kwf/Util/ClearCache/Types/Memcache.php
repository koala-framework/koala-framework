<?php
class Kwf_Util_ClearCache_Types_Memcache extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        $cache = Kwf_Cache::factory('Core', 'Memcached', array(
            'lifetime'=>null,
            'automatic_cleaning_factor' => false,
            'automatic_serialization'=>true));
        $cache->clean();
    }

    public function getTypeName()
    {
        return 'memcache';
    }
    public function doesClear() { return true; }
    public function doesRefresh() { return false; }
}
