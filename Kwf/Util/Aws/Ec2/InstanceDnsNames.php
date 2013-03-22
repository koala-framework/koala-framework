<?php
class Kwf_Util_Aws_Ec2_InstanceDnsNames
{
    public static function getOther()
    {
        $ec2 = new Kwf_Util_Aws_Ec2();
        $r = $ec2->describe_instances(array(
            'Filter' => array(
                array(
                    'Name' => 'tag:application.id',
                    'Value' => Kwf_Config::getValue('application.id'),
                ),
                array(
                    'Name' => 'tag:config_section',
                    'Value' => Kwf_Setup::getConfigSection(),
                )
            )
        ));
        if (!$r->isOK()) {
            throw new Kwf_Exception($r->body->asXml());
        }

        $ownHostname = file_get_contents('http://169.254.169.254/latest/meta-data/public-hostname');
        $domains = array();
        foreach ($r->body->reservationSet->item as $resItem) {
            foreach ($resItem->instancesSet->item as $item) {
                $dnsName = (string)$item->dnsName;
                if ($dnsName && $dnsName != $ownHostname) {
                    $domains[] = $dnsName;
                }
            }
        }
        return array_unique($domains);
    }
}
