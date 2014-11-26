<?php
class Kwf_Model_Factory_Abstract
{
    public static function processConfig(array $config)
    {
        throw new Kwf_Exception('processConfig not possible for this factory');
    }

    public static function getModelInstance($config)
    {
        if (!$config) {
            throw new Kwf_Exception("no config given");
        }
        if ($config['type'] == 'ClassName' || $config['type'] == 'Proxied' || $config['type'] == 'UnionSource') {
            $cls = 'Kwf_Model_Factory_'.$config['type'];
        } else {
            $cls = $config['type'];
        }
        return call_user_func(array($cls, 'getModelInstance'), $config);
    }
}
