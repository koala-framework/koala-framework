<?php
class Vps_Benchmark_Profile
{
    private $_start;
    private $_stop;
    private $_queriesStart;
    private $_queriesStop;
    private $_memoryStart;
    private $_memoryStop;
    public $counter;
    public $identifier;
    public $duration;
    public $queries;
    public $stopped = false;

    //sollte über Vps_Benchmark::start aufgerufen werden
    public function __construct($identifier, $addInfo)
    {
        if (!$identifier && function_exists('debug_backtrace')) {
            if (function_exists('memory_get_usage')) {
                $this->_memoryStart = memory_get_usage();
            }
            $bt = debug_backtrace();
            if (isset($bt[2]['function'])) {
                $identifier = $bt[2]['function'];
            }
            if (isset($bt[2]['args'])) {
                $identifier .= '(';
                foreach ($bt[2]['args'] as $i=>$a) {
                    if (is_array($a)) {
                        foreach ($a as &$ai) {
                            if (is_array($ai)) $ai = 'Array';
                            if (is_object($ai)) $ai = 'Object';
                        }
                        $a = implode(', ', $a);
                    }
                    if (is_object($a) && method_exists($a, 'toDebug')) $a = $a->toDebug();
                    if (is_object($a) && method_exists($a, '__toString')) $a = $a->__toString();
                    if (is_object($a)) $a = '('.get_class($a).')';
                    if ($i > 0) $identifier .= ', ';
                    $identifier .= (string)$a;
                }
                if (strlen($identifier) > 100) $identifier = substr($identifier, 0, 100).'...';
                $identifier .= ')';
            }
            if (isset($bt[3]['function'])) {
                $identifier .= ', '.$bt[3]['function'];
            }
        }
        $this->identifier = $identifier;
        $this->addInfo = $addInfo;
        $this->_start = microtime(true);
        if (Zend_Registry::get('db')->getProfiler() instanceof Vps_Db_Profiler) {
            $this->_queriesStart =
                Zend_Registry::get('db')->getProfiler()->getQueryCount();
        }
        Vps_Benchmark::$benchmarks[] = $this;
    }

    public function getOutput()
    {
        $out = array();
        $out[] = round($this->duration, 3).' sec';

        if (isset($this->memory)) {
            $out[] = $this->memory.' Bytes';
        }
        if (isset($this->queries)) {
            $this->queries = $this->_queriesStop - $this->_queriesStart;
            $out[] = $this->queries.' DB-Queries';
        }
        foreach ($this as $k=>$i) {
            if ($k == 'identifier' || $k == 'duration' || $k == 'queries' || $k == 'memory' || $k == 'counter' || $k == 'addInfo' || $k == 'stopped') continue;
            if (substr($k, 0, 1) == '_') continue;
            $out[] = $k.': '.$i;
        }
        return $out;
    }


    public function __destruct()
    {
        if (!$this->stopped) $this->stop();
    }

    /**
     * Beendet eine Sequenz
     *
     */
    public function stop()
    {
        $this->_stop = microtime(true);
        $this->duration = $this->_stop - $this->_start;
        if (function_exists('memory_get_usage')) {
            $this->_memoryStop = memory_get_usage();
            $this->memory = $this->_memoryStop - $this->_memoryStart;
        }
        if (Zend_Registry::get('db')->getProfiler() instanceof Vps_Db_Profiler) {
            $this->_queriesStop =  Zend_Registry::get('db')->getProfiler()->getQueryCount();
            $this->queries = $this->_queriesStop - $this->_queriesStart;
        }

        if (Zend_Registry::get('config')->debug->firephp && class_exists('FirePHP') && FirePHP::getInstance() && FirePHP::getInstance()->detectClientExtension()) {
            p($this->identifier.': '.$this->addInfo.' '.implode('; ', $this->getOutput()));
        }
        $this->stopped = true;
    }
}
