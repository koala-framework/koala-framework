<?php
class Kwf_Util_Aws_ElastiCache_CacheBackend extends Zend_Cache_Backend_Memcached
{
    public function __construct(array $options = array())
    {
        if (!isset($options['cacheClusterId'])) {
            throw new Kwf_Exception('required parameter: cacheClusterId');
        }
        $ec = new Kwf_Util_Aws_ElastiCache();
        $r = $ec->describe_cache_clusters(array(
            'ShowCacheNodeInfo' => true,
            'CacheClusterId' => $options['cacheClusterId']
        ));
        if (!$r->isOk()) {
            if (isset($r->body->Error->Message)) {
                throw new Kwf_Exception($r->body->Error->Message);
            } else {
                throw new Kwf_Exception("Getting CacheClusters failed");
            }
        }
        $options['servers'] = array();
        foreach ($r->body->DescribeCacheClustersResult->CacheClusters->CacheCluster->CacheNodes->CacheNode as $node) {
            $options['servers'][] = array(
                'host' => (string)$node->Endpoint->Address,
                'port' => (int)$node->Endpoint->Port,
            );
        }
        parent::__construct($options);
    }
}
