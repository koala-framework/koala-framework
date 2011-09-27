<?php
class Vps_Util_Gearman_Client extends GearmanClient
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $i = new self();
            $c = Vps_Registry::get('config')->server->gearman;
            foreach ($c->jobServers as $k=>$server) {
                if ($server) {
                    Vps_Util_Gearman_AdminClient::checkConnection($server);
                    if ($server->tunnelUser) {
                        $i->addServer('localhost', 4730);
                    } else {
                        $i->addServer($server->host, $server->port);
                    }
                }
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

    
    public function addTask($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addTask($function_name, $workload, $context, $unique);
    }

    public function addTaskBackground($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addTaskBackground($function_name, $workload, $context, $unique);
    }

    public function addTaskHigh($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addTaskHigh($function_name, $workload, $context, $unique);
    }

    public function addTaskHighBackground($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addTaskHighBackground($function_name, $workload, $context, $unique);
    }

    public function addTaskLow($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addTaskLow($function_name, $workload, $context, $unique);
    }
    
    public function addTaskLowBackground($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::addTaskLowBackground($function_name, $workload, $context, $unique);
    }

    //ACHTUNG immer doVps statt do verwenden! (do kann leider nicht überschrieben werden)
    public function doVps($function_name, $workload, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return $this->do($function_name, $workload, $unique);
    }

    public function doBackground($function_name, $workload, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::doBackground($function_name, $workload, $unique);
    }
    
    public function doHigh($function_name, $workload, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::doHigh($function_name, $workload, $unique);
    }

    public function doHighBackground($function_name, $workload, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::doHighBackground($function_name, $workload, $unique);
    }

    public function doLow($function_name, $workload, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::doLow($function_name, $workload, $unique);
    }

    public function doLowBackground($function_name, $workload, $unique=null)
    {
        $function_name = self::_processFunctionName($function_name);
        return parent::doLowBackground($function_name, $workload, $unique);
    }
}
