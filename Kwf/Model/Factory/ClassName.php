<?php
class Kwf_Model_Factory_ClassName extends Kwf_Model_Factory_Abstract
{
    private static $_instances = array();

    public static function getModelInstance($config)
    {
        if (is_array($config)) {
            $config = $config['id'];
        }
        if (!isset(self::$_instances[$config])) {
            $ret = new $config();
            $ret->setFactoryConfig(array(
                'type' => 'ClassName',
                'id' => $config,
            ));
            self::$_instances[$config] = $ret;
        }
        return self::$_instances[$config];
    }

    public static function clearInstances()
    {
        self::$_instances = array();
    }
}
