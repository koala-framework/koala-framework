<?php
class Kwf_Model_Factory_UnionSource extends Kwf_Model_Factory_Abstract
{
    public static function processConfig(array $config)
    {
        if (isset($config['id'])) {
            throw new Kwf_Exception("id is already set in config");
        }
        if (!is_object($config['union'])) {
            throw new Kwf_Exception("Can't create factory config");
        }
        $fc = $config['union']->getFactoryConfig();
        $config['id'] = $fc['id'].'.union'.$config['modelKey'];
        $config['union'] = $fc;
        return $config;
    }

    public static function getModelInstance($config)
    {
        $models = Kwf_Model_Factory_Abstract::getModelInstance($config['union'])->getUnionModels();
        return $models[$config['modelKey']];
    }
}
