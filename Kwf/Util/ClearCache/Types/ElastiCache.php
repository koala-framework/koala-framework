<?php
class Kwf_Util_ClearCache_Types_ElastiCache extends Kwf_Util_ClearCache_Types_Abstract
{
    public function clearCache($options)
    {
        $skipOtherServers = isset($options['skipOtherServers']) ? $options['skipOtherServers'] : false;
        if (!$skipOtherServers) {
            //namespace used in Kwf_Cache_Simple
            $cache = Kwf_Cache_Simple::getZendCache();
            $mc = $cache->getBackend()->getMemcache();
            if ($mc->get('cache_namespace')) {
                $mc->increment('cache_namespace');
            }
        }
    }

    public function getTypeName()
    {
        return 'elastiCache';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
