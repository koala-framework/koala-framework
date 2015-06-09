<?php
class Kwf_Util_Aws_Ec2 extends AmazonEC2
{
    public function __construct(array $options = array())
    {
        if (!isset($options['default_cache_config'])) $options['default_cache_config'] = 'cache/aws';
        if (!isset($options['key']) && Kwf_Config::getValue('aws.key')) $options['key'] = Kwf_Config::getValue('aws.key');
        if (!isset($options['secret']) && Kwf_Config::getValue('aws.secret')) $options['secret'] = Kwf_Config::getValue('aws.secret');
        parent::__construct($options);
    }
}
