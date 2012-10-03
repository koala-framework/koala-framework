<?php
require_once Kwf_Config::getValue('externLibraryPath.aws').'/sdk.class.php';
class Kwf_Util_Aws_ElastiCache extends AmazonElastiCache
{
    public function __construct(array $options = array())
    {
        if (!isset($options['default_cache_config'])) $options['default_cache_config'] = 'cache/aws';
        if (!isset($options['key'])) $options['key'] = Kwf_Config::getValue('aws.key');
        if (!isset($options['secret'])) $options['secret'] = Kwf_Config::getValue('aws.secret');
        parent::__construct($options);
    }

    private static function _getCacheClusterEndpointsCache()
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

    private static function _getCacheClusterEndpointsCacheId($cacheClusterId)
    {
        return 'aws_eceps_'.str_replace('-', '_', $cacheClusterId);
    }

    //uncached, use getCacheClusterEndpointsCached to use cache
    public static function getCacheClusterEndpoints($cacheClusterId)
    {

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

    public static function refreshCacheClusterEndpoints($cacheClusterId)
    {
        $servers = self::getCacheClusterEndpoints($cacheClusterId);
        self::_getCacheClusterEndpointsCache()->save($servers, self::_getCacheClusterEndpointsCacheId($cacheClusterId));
        return $servers;
    }

    //if used you need to refresh this cache yourself
    public static function getCacheClusterEndpointsCached($cacheClusterId)
    {
        $cacheId = self::_getCacheClusterEndpointsCacheId($cacheClusterId);
        $servers = self::_getCacheClusterEndpointsCache()->load($cacheId);
        if ($servers === false) {
            $servers = self::refreshCacheClusterEndpoints($cacheClusterId);
        }
        return $servers;
    }
}
