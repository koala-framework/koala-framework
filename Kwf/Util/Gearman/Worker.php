<?php
class Kwf_Util_Gearman_Worker extends GearmanWorker
{
    private $_functionPrefix;

    public static function createInstance($group = null)
    {
        $i = new self();

        if (!$group) $c = Kwf_Config::getValueArray('server.gearman');
        else $c = Kwf_Config::getValueArray('server.gearmanGroup.'.$group);

        $i->_functionPrefix = $c['functionPrefix'];

        foreach ($c['jobServers'] as $server) {
            if ($server) {
                Kwf_Util_Gearman_AdminClient::checkConnection($server);
                if (isset($server['tunnelUser']) && $server['tunnelUser']) {
                    $i->addServer('localhost', 4730);
                } else {
                    $i->addServer($server['host'], $server['port']);
                }
            }
        }

        return $i;
    }
    private function _processFunctionName($fn)
    {
        return $this->_functionPrefix.'_'.$fn;
    }


    public function addFunction($function_name, $function, &$context=null, $timeout=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addFunction($function_name, $function, $context, $timeout);
    }

    public function register($function_name, $timeout)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::register($function_name, $timeout);
    }

    public function unregister($function_name)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::unregister($function_name);
    }
}
