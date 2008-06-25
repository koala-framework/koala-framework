<?php
class Vps_Benchmark
{
    private $_start;
    private $_stop;
    private $_queriesStart;
    private $_queriesStop;
    private $_memoryStart;
    private $_memoryStop;
    public $identifier;
    public $duration;
    public $queries;
    private $_stopped = false;

    private function __construct($identifier = null)
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
                        }
                        $a = implode(', ', $a);
                    }
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
        $this->_start = microtime(true);
        if (Zend_Registry::get('db')->getProfiler() instanceof Vps_Db_Profiler) {
            $this->_queriesStart =
                Zend_Registry::get('db')->getProfiler()->getQueryCount();
        }
    }

    /**
     * Startet eine Sequenz
     *
     * @param string $identifier
     */
    public static function start($identifier = null)
    {
        if (!Vps_Registry::get('config')->debug->benchmark) return null;
        return new Vps_Benchmark($identifier);
    }

    /**
     * Beendet eine Sequenz
     *
     */
    public function stop()
    {
        $out = array();
        $this->_stop = microtime(true);
        $this->duration = $this->_stop - $this->_start;
        $out[] = round($this->duration, 3).' sec';

        if (function_exists('memory_get_usage')) {
            $this->_memoryStop = memory_get_usage();
            $this->memory = $this->_memoryStop - $this->_memoryStart;
            $out[] = $this->memory.' Bytes';
        }
        if (Zend_Registry::get('db')->getProfiler() instanceof Vps_Db_Profiler) {
            $this->_queriesStop =  Zend_Registry::get('db')->getProfiler()->getQueryCount();
            $this->queries = $this->_queriesStop - $this->_queriesStart;
            $out[] = $this->queries.' DB-Queries';
        }
        
        foreach ($this as $k=>$i) {
            if ($k == 'identifier' || $k == 'duration' || $k == 'queries' || $k == 'memory') continue;
            if (substr($k, 0, 1) == '_') continue;
            $out[] = $k.': '.$i;
        }
        p($this->identifier.': '.implode('; ', $out));
        $this->_stopped = true;
    }

    public function __destruct()
    {
        if (!$this->_stopped) $this->stop();
    }
}
