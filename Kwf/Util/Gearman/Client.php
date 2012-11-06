<?php
class Kwf_Util_Gearman_Client extends GearmanClient
{
    private $_functionPrefix;

    /**
     * @return self
     */
    public static function getInstance($group = null)
    {
        static $i = array();
        if (!isset($i[$group])) {
            $i[$group] = new self();

            $c = Kwf_Util_Gearman_Servers::getServersTryConnect($group);

            $i[$group]->_functionPrefix = $c['functionPrefix'];

            shuffle($c['jobServers']);
            foreach ($c['jobServers'] as $server) {
                $i[$group]->addServer($server['host'], $server['port']);
            }
        }
        return $i[$group];
    }

    /**
     * @return self
     */
    public static function getInstanceCached($group = null)
    {
        static $i = array();
        if (!isset($i[$group])) {
            $i[$group] = new self();

            $c = Kwf_Util_Gearman_Servers::getServersCached($group);

            $i[$group]->_functionPrefix = $c['functionPrefix'];

            shuffle($c['jobServers']);
            foreach ($c['jobServers'] as $server) {
                $i[$group]->addServer($server['host'], $server['port']);
            }
        }
        return $i[$group];
    }

    private function _processFunctionName($fn)
    {
        return $this->_functionPrefix.'_'.$fn;
    }

    public function addTask($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addTask($function_name, $workload, $context, $unique);
    }

    public function addTaskBackground($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addTaskBackground($function_name, $workload, $context, $unique);
    }

    public function addTaskHigh($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addTaskHigh($function_name, $workload, $context, $unique);
    }

    public function addTaskHighBackground($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addTaskHighBackground($function_name, $workload, $context, $unique);
    }

    public function addTaskLow($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addTaskLow($function_name, $workload, $context, $unique);
    }
    
    public function addTaskLowBackground($function_name, $workload, &$context=null, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::addTaskLowBackground($function_name, $workload, $context, $unique);
    }

    //ACHTUNG immer doKwf statt do verwenden! (do kann leider nicht überschrieben werden)
    public function doKwf($function_name, $workload, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        if (method_exists($this, 'doNormal')) {
            return $this->doNormal($function_name, $workload, $unique);
        } else {
            //older versions of gearman extension
            return $this->do($function_name, $workload, $unique);
        }
    }

    public function doBackground($function_name, $workload, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::doBackground($function_name, $workload, $unique);
    }
    
    public function doHigh($function_name, $workload, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::doHigh($function_name, $workload, $unique);
    }

    public function doHighBackground($function_name, $workload, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::doHighBackground($function_name, $workload, $unique);
    }

    public function doLow($function_name, $workload, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::doLow($function_name, $workload, $unique);
    }

    public function doLowBackground($function_name, $workload, $unique=null)
    {
        $function_name = $this->_processFunctionName($function_name);
        return parent::doLowBackground($function_name, $workload, $unique);
    }
}
