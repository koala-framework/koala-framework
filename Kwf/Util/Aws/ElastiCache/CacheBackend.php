<?php
class Kwf_Util_Aws_ElastiCache_CacheBackend extends Zend_Cache_Backend_Memcached
{
    public function __construct(array $options = array())
    {
        if (!isset($options['cacheClusterId'])) {
            throw new Kwf_Exception('required parameter: cacheClusterId');
        }
        $options['servers'] = Kwf_Util_Aws_ElastiCache_CacheClusterEndpoints::getCached($options['cacheClusterId']);
        parent::__construct($options);
    }

    public function getMemcache()
    {
        return $this->_memcache;
    }
}
