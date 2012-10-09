<?php
class Kwf_Util_Aws_ElastiCache_CacheClusterEndpoints
{
    private static function _getCache()
    {
        static $i;
        if (!$i) {
            $i = Kwf_Cache::factory(
                'Core',
                'File',
                array('lifetime' => null, 'automatic_serialization' => true),
                array('cache_dir' => 'cache/aws')
            );
        }
        return $i;
    }

    private static function _getCacheId($cacheClusterId)
    {
        return 'aws_eceps_'.str_replace('-', '_', $cacheClusterId);
    }

    //uncached, use getCached to use cache
    public static function get($cacheClusterId)
    {
        if (!$cacheClusterId) throw new Kwf_Exception("cacheClusterId is requried");
        $ec = new Kwf_Util_Aws_ElastiCache();
        $r = $ec->describe_cache_clusters(array(
            'ShowCacheNodeInfo' => true,
            'CacheClusterId' => $cacheClusterId
        ));
        if (!$r->isOk()) {
            if (isset($r->body->Error->Message)) {
                throw new Kwf_Exception($r->body->Error->Message);
            } else {
                throw new Kwf_Exception("Getting CacheClusters failed");
            }
        }
        $servers = array();
        foreach ($r->body->DescribeCacheClustersResult->CacheClusters->CacheCluster->CacheNodes->CacheNode as $node) {
            $servers[] = array(
                'host' => (string)$node->Endpoint->Address,
                'port' => (int)$node->Endpoint->Port,
            );
        }
        return $servers;
    }

    public static function refreshCache($cacheClusterId)
    {
        $servers = self::get($cacheClusterId);
        self::_getCache()->save($servers, self::_getCacheId($cacheClusterId));
        return $servers;
    }

    //if used you need to refresh this cache yourself
    public static function getCached($cacheClusterId)
    {
        $cacheId = self::_getCacheId($cacheClusterId);
        $servers = self::_getCache()->load($cacheId);
        if ($servers === false) {
            $servers = self::refresh($cacheClusterId);
        }
        return $servers;
    }
}
