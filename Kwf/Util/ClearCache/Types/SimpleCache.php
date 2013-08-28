<?php
class Kwf_Util_ClearCache_Types_SimpleCache extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        $skipOtherServers = isset($options['skipOtherServers']) ? $options['skipOtherServers'] : false;
        if (!$skipOtherServers) {
            //namespace used in Kwf_Cache_Simple
            $mc = Kwf_Cache_Simple::getMemcache();
            if ($mc->get(Kwf_Cache_Simple::getUniquePrefix().'cache_namespace')) {
                $mc->increment(Kwf_Cache_Simple::getUniquePrefix().'cache_namespace');
            }
        }
    }

    public function getTypeName()
    {
        return 'simpleCache';
    }
    public function doesRefresh() { return false; }
    public function doesClear() { return true; }
}
