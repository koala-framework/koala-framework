<?php
class Kwf_Util_Aws_ElastiCache_Counter extends Kwf_Benchmark_Counter_Memcache
{
    public function __construct($config = array())
    {
        if (isset($config['cacheCluster'])) {
            $memcache = new Memcache;
            $servers = Kwf_Util_Aws_ElastiCache_CacheClusterEndpoints::getCached($config['cacheCluster']);
            foreach ($servers as $s) {
                if (version_compare(phpversion('memcache'), '2.1.0') == -1 || phpversion('memcache')=='2.2.4') { // < 2.1.0
                    $memcache->addServer($s['host'], $s['port'], true, 1, 1, 1);
                } else if (version_compare(phpversion('memcache'), '3.0.0') == -1) { // < 3.0.0
                    $memcache->addServer($s['host'], $s['port'], true, 1, 1, 1, true, null, 10000);
                } else {
                    $memcache->addServer($s['host'], $s['port'], true, 1, 1, 1);
                }
            }
            $config['memcache'] = $memcache;
        }

        parent::__construct($config);
    }
}
