<?php
class Kwf_Util_Gearman_Servers
{
    public static function getServersCached($group = null)
    {
        $servers = self::_getCache()->load(self::_getCacheId($group));
        if ($servers === false) {
            $servers = self::refreshCache($group);
        }
        return $servers;
    }

    public static function refreshCache($group)
    {
        $servers = self::getServersTryConnect($group);
        self::_getCache()->save($servers, self::_getCacheId($group));
        return $servers;
    }

    public static function getServersTryConnect($group = null)
    {
        $ret = self::_getServers($group);
        $ret['jobServers'] = self::tryConnect($ret['jobServers']);
        return $ret;
    }

    public static function checkServers($group)
    {
    }

    private static function _getServers($group)
    {
        if (!$group) $c = Kwf_Config::getValueArray('server.gearman');
        else $c = Kwf_Config::getValueArray('server.gearmanGroup.'.$group);

        $ret['functionPrefix'] = $c['functionPrefix'];

        if (isset($c['awsAutoScalingGroup']) && $c['awsAutoScalingGroup']) {
            if (isset($c['jobServers']) && $c['jobServers']) {
                throw new Kwf_Exception("Don't use awsAutoScalingGroup and jobServers together");
            }
            $servers = Kwf_Util_Aws_AutoScaling_InstanceDnsNames::get($c['awsAutoScalingGroup']);
            $ret['jobServers'] = array();
            foreach ($servers as $s) {
                $ret['jobServers'][] = array('host'=>$s, 'port'=>4730);
            }
        } else {
            foreach ($c['jobServers'] as $server) {
                if ($server) {
                    Kwf_Util_Gearman_AdminClient::checkConnection($server);
                    if (isset($server['tunnelUser']) && $server['tunnelUser']) {
                        $ret['jobServers'][] = array('host'=>'localhost', 'port'=>4730);
                    } else {
                        if (!isset($server['port'])) $server['port'] = 4730;
                        $ret['jobServers'][] = array('host'=>$server['host'], 'port'=>$server['port']);
                    }
                }
            }
        }
        return $ret;
    }

    public static function getGroups()
    {
        $groups = Kwf_Config::getValueArray('server.gearmanGroup');
        $ret = array_keys($groups);

        $noGroup = Kwf_Config::getValueArray('server.gearman');
        if ($noGroup && $noGroup['jobServers']) {
            $servers = array_values($noGroup['jobServers']);
            if ($servers[0]) {
                $ret[] = null; //no group
            }
        }
        return $ret;
    }

    public static function tryConnect($jobServers)
    {
        foreach ($jobServers as $k=>$i) {
            $ok = false;
            try {
                if (fsockopen($i['host'], $i['port'], $errno, $errstr, 2)) {
                    $ok = true;
                }
            } catch (Exception $e) {}
            if (!$ok) {
                unset($jobServers[$k]);
            }
        }
        return array_values($jobServers);
    }

    private static function _getCache()
    {
        static $i;
        if (!$i) {
            $i = Kwf_Cache::factory(
                'Core',
                'File',
                array('lifetime' => null, 'automatic_serialization' => true),
                array('cache_dir' => 'cache/config')
            );
        }
        return $i;
    }

    private static function _getCacheId($group)
    {
        return 'german_inst_'.str_replace('-', '_', $group);
    }
}
