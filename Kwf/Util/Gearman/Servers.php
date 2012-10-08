<?php
class Kwf_Util_Gearman_Servers
{
    public static function getServers($group = null)
    {
        if (!$group) $c = Kwf_Config::getValueArray('server.gearman');
        else $c = Kwf_Config::getValueArray('server.gearmanGroup.'.$group);

        $ret['functionPrefix'] = $c['functionPrefix'];

        if (isset($c['awsAutoScalingGroup']) && $c['awsAutoScalingGroup']) {
            if (isset($c['jobServers']) && $c['jobServers']) {
                throw new Kwf_Exception("Don't use awsAutoScalingGroup and jobServers together");
            }
            $servers = Kwf_Util_Aws_AutoScaling_InstanceDnsNames::getCached($c['awsAutoScalingGroup']);
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
}
