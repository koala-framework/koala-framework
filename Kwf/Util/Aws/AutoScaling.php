<?php
require_once Kwf_Config::getValue('externLibraryPath.aws').'/sdk.class.php';
class Kwf_Util_Aws_AutoScaling extends AmazonAS
{
    public function __construct(array $options = array())
    {
        if (!isset($options['default_cache_config'])) $options['default_cache_config'] = 'cache/aws';
        if (!isset($options['key'])) $options['key'] = Kwf_Config::getValue('aws.key');
        if (!isset($options['secret'])) $options['secret'] = Kwf_Config::getValue('aws.secret');
        parent::__construct($options);
    }

    private static function _getInstanceDnsNamesCache()
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

    private static function _getInstanceDnsNamesCacheId($autoScalingGroup)
    {
        return 'aws_as_inst_dns_'.str_replace('-', '_', $autoScalingGroup);
    }

    //uncached, use getInstanceDnsNamesCached to use cache
    public static function getInstanceDnsNames($autoScalingGroup)
    {
        $ac = new Kwf_Util_Aws_AutoScaling();
        $r = $ac->describe_auto_scaling_groups(array(
            'AutoScalingGroupNames' => $autoScalingGroup,
        ));
        if (!$r->isOK()) {
            throw new Kwf_Exception($r->body->asXml());
        }
        $instanceIds = array();
        foreach ($r->body->DescribeAutoScalingGroupsResult->AutoScalingGroups->member->Instances->member as $member) {
            $instanceIds[] = (string)$member->InstanceId;
        }

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

    public static function refreshInstanceDnsNamesCache($autoScalingGroup)
    {
        $servers = self::getInstanceDnsNames($autoScalingGroup);
        self::_getInstanceDnsNamesCache()->save($servers, self::_getInstanceDnsNamesCacheId($autoScalingGroup));
        return $servers;
    }

    //if used you need to refresh this cache yourself
    public static function getCacheClusterEndpointsCached($autoScalingGroup)
    {
        $cacheId = self::_getInstanceDnsNamesCacheId($autoScalingGroup);
        $servers = self::_getInstanceDnsNamesCache()->load($cacheId);
        if ($servers === false) {
            $servers = self::refreshInstanceDnsNamesCache($autoScalingGroup);
        }
        return $servers;
    }

}
