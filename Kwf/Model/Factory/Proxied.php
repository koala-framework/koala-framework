<?php
class Kwf_Model_Factory_Proxied extends Kwf_Model_Factory_Abstract
{
    public static function processConfig(array $config)
    {
        if (isset($config['id'])) {
            throw new Kwf_Exception("id is already set in config");
        }
        if (!is_object($config['proxy'])) {
            throw new Kwf_Exception("Can't create factory config");
        }
        $fc = $config['proxy']->getFactoryConfig();
        $config['id'] = $fc['id'].'.proxied';
        $config['proxy'] = $fc;
        return $config;
    }

    public static function getModelInstance($config)
    {
        return Kwf_Model_Factory_Abstract::getModelInstance($config['proxy'])->getProxyModel();
    }
}
