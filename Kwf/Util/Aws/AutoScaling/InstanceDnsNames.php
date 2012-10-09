<?php
class Kwf_Util_Aws_AutoScaling_InstanceDnsNames
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

    private static function _getCacheId($autoScalingGroup)
    {
        return 'aws_as_inst_dns_'.str_replace('-', '_', $autoScalingGroup);
    }

    //uncached, use getCached to use cache
    public static function get($autoScalingGroup)
    {
        $ac = new Kwf_Util_Aws_AutoScaling();
        $r = $ac->describe_auto_scaling_groups(array(
            'AutoScalingGroupNames' => $autoScalingGroup,
        ));
        if (!$r->isOK()) {
            throw new Kwf_Exception($r->body->asXml());
        }
        $instanceIds = array();
        if ($r->body->DescribeAutoScalingGroupsResult->AutoScalingGroups->member) {
            foreach ($r->body->DescribeAutoScalingGroupsResult->AutoScalingGroups->member->Instances->member as $member) {
                $instanceIds[] = (string)$member->InstanceId;
            }
        }
        if (!$instanceIds) return array();

        $ec2 = new Kwf_Util_Aws_Ec2();
        $r = $ec2->describe_instances(array(
            'InstanceId' => $instanceIds
        ));
        if (!$r->isOK()) {
            throw new Kwf_Exception($r->body->asXml());
        }
        $servers = array();
        foreach ($r->body->reservationSet->item as $reservaionSet) {
            foreach ($reservaionSet->instancesSet->item as $item) {
                $dnsName = (string)$item->dnsName;
                if ($dnsName) $servers[] = $dnsName;
            }
        }
        return $servers;
    }

    public static function refreshCache($autoScalingGroup)
    {
        $servers = self::get($autoScalingGroup);
        self::_getCache()->save($servers, self::_getCacheId($autoScalingGroup));
        return $servers;
    }

    //if used you need to refresh this cache yourself
    public static function getCached($autoScalingGroup)
    {
        $cacheId = self::_getCacheId($autoScalingGroup);
        $servers = self::_getCache()->load($cacheId);
        if ($servers === false) {
            $servers = self::refreshCache($autoScalingGroup);
        }
        return $servers;
    }
}
