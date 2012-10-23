<?php
class Kwf_Util_Aws_AutoScaling_InstanceDnsNames
{
    public static function get($autoScalingGroup)
    {
        if (!$autoScalingGroup) throw new Kwf_Exception("autoScalingGroup is requried");
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
}
