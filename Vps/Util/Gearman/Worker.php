<?php
class Vps_Util_Gearman_Worker extends GearmanWorker
{
    public static function createInstance()
    {
        $i = new self();
        $c = Vps_Registry::get('config')->server->gearman;
        foreach ($c->jobServers as $server) {
            if ($server) {
                $i->addServer($server->host, $server->port);
            }
        }
        return $i;
    }

    private static function _processFunctionName($fn)
    {
        static $prefix;
        if (!isset($prefix)) {
            $prefix = Vps_Registry::get('config')->server->gearman->functionPrefix;
        }
        return $prefix.'_'.$fn;
    }

    public function addFunction($function_name, $function, &$context=null, $timeout=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addFunction($function_name, $function, $context, $timeout);
    }

    public function register($function_name, $timeout)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::register($function_name, $timeout);
    }

    public function unregister($function_name)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::unregister($function_name);
    }
}
