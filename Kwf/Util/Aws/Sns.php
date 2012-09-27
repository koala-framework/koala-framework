<?php
require_once Kwf_Config::getValue('externLibraryPath.aws').'/sdk.class.php';
class Kwf_Util_Aws_Sns extends AmazonSNS
{
    public function __construct(array $options = array())
    {
        if (!isset($options['default_cache_config'])) $options['default_cache_config'] = 'cache/aws';
        if (!isset($options['key'])) $options['key'] = Kwf_Config::getValue('aws.key');
        if (!isset($options['secret'])) $options['secret'] = Kwf_Config::getValue('aws.secret');
        parent::__construct($options);
    }
}
