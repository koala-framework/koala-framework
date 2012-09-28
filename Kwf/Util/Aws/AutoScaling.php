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

    public static function getInstanceDnsNames($autoScalingGroup)
    {
        $cacheId = 'aws-as-inst-dns-'.$autoScalingGroup;
        $servers = Kwf_Cache_Simple::fetch($cacheId);
        if ($servers === false) {
            $ac = new Kwf_Util_Aws_AutoScaling();
            $r = $ac->describe_auto_scaling_groups(array(
                'AutoScalingGroupNames' => $autoScalingGroup,
            ));
            foreach ($r->body->DescribeAutoScalingGroupsResult->AutoScalingGroups->member->Instances->member as $member) {
                $instanceIds[] = (string)$member->InstanceId;
            }

            $ec2 = new Kwf_Util_Aws_Ec2();
            $r = $ec2->describe_instances(array(
                'InstanceId' => $instanceIds
            ));
            $servers = array();
            foreach ($r->body->reservationSet->item as $item) {
                $dnsName = (string)$item->instancesSet->item->dnsName;
                if ($dnsName) $servers[] = $dnsName;
            }
            Kwf_Cache_Simple::add($cacheId, $servers, 60*10);
        }
        return $servers;
    }
}
