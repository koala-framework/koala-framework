<?php
class Vps_Benchmark
{
    private static $_startTime;
    private static $_enabled = false;
    private static $_logEnabled = false;
    private static $_counter;

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
        if (!self::$_enabled) return null;
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
        if (Zend_Registry::get('config')->debug->firephp && class_exists('FirePHP') && FirePHP::getInstance() && FirePHP::getInstance()->detectClientExtension()) {
            p($this->identifier.': '.implode('; ', $out));
        } else {
            //TODO
        }
        $this->_stopped = true;
    }

    public function __destruct()
    {
        if (!$this->_stopped) $this->stop();
    }


    public static function enable()
    {
        if (!isset(self::$_startTime)) self::$_startTime = microtime(true);
        self::$_enabled = true;
    }

    public static function enableLog()
    {
        if (!isset(self::$_startTime)) self::$_startTime = microtime(true);
        self::$_logEnabled = true;
    }

    public static function isEnabled()
    {
        return self::$_enabled;
    }

    public static function count($name, $value = null)
    {
        if (!self::$_enabled && !self::$_logEnabled) return false;
        if (!isset(self::$_counter[$name])) {
            if ($value) {
                self::$_counter[$name] = array();
            } else {
                self::$_counter[$name] = 0;
            }
        }
        if ($value) {
            if (!is_array(self::$_counter[$name])) {
                throw new Vps_Exception("Missing value for counter '$name'");
            }
            self::$_counter[$name][] = $value;;
        } else {
            if (is_array(self::$_counter[$name])) {
                throw new Vps_Exception("no value possible for counter '$name'");
            }
            self::$_counter[$name]++;
        }
    }

    public static function output()
    {
        if (self::$_logEnabled) {
            $fields = false;
            $newFile = true;
            if (file_exists('benchmark')) {
                $newFile = false;
                $fp = fopen('benchmark', 'r');
                $fields = fgetcsv($fp, 1024, ';');
                fclose($fp);
            }
            if ($fields) {
                foreach (array_keys(self::$_counter) as $i) {
                    if (!in_array($i, $fields)) {
                        $newFile = true;
                        $fields = false;
                        break;
                    }
                }
            }
            if (!$fields) {
                $fields = array('date', 'url', 'duration', 'memory', 'queries');
                foreach (self::$_counter as $k=>$i) {
                    $fields[] = $k;
                }
            }
            $fp = fopen('benchmark', 'a');
            if ($newFile) {
                fwrite($fp, implode(';', $fields)."\n");
            }
            $out = array();
            foreach ($fields as $i) {
                if ($i == 'date') {
                    $out[] = date('Y-m-d H:i:s');
                } else if ($i == 'url') {
                    $out[] = $_SERVER['REQUEST_URI'];
                } else if ($i == 'duration') {
                    $out[] = round(microtime(true) - self::$_startTime, 2);
                } else if ($i == 'memory') {
                    $out[] = round(memory_get_peak_usage()/1024);
                } else if ($i == 'queries') {
                    if (Zend_Registry::get('db')->getProfiler() && method_exists(Zend_Registry::get('db')->getProfiler(), 'getQueryCount')) {
                        $out[] = Zend_Registry::get('db')->getProfiler()->getQueryCount();
                    } else {
                        $out[] = '';
                    }
                } else if (!isset(self::$_counter[$i])) {
                    $out[] = 0;
                } else if (is_array(self::$_counter[$i])) {
                    $out[] = count(self::$_counter[$i]);
                } else {
                    $out[] = self::$_counter[$i];
                }
            }
            fwrite($fp, implode(';', $out)."\n");
            fclose($fp);
        } else if (self::$_enabled) {
            echo '<div style="font-family:Verdana;font-size:10px;background-color:white;width:200px;position:absolute;top:0;right:0;padding:5px;">';
            echo round(microtime(true) - self::$_startTime, 2)." sec<br />\n";
            echo "Memory: ".round(memory_get_peak_usage()/1024)." kb<br />\n";
            if (Zend_Registry::get('db')->getProfiler() && method_exists(Zend_Registry::get('db')->getProfiler(), 'getQueryCount')) {
                echo "DB-Queries: ".Zend_Registry::get('db')->getProfiler()->getQueryCount()."<br />\n";
            }
            foreach (self::$_counter as $k=>$i) {
                if (is_array($i)) {
                    echo "<a style=\"display:block;\"href=\"#\" onclick=\"this.nextSibling.style.display='block';return(false);\">";
                    echo "$k: ".count($i)."</a>";
                    echo "<ul style=\"display:none\">";
                    foreach ($i as $j) {
                        echo "<li>$j</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "$k: $i<br />\n";
                }
            }
            echo "</div>";
        }
    }

    public static function info($msg)
    {
        if (!self::$_enabled) return;
        if (Zend_Registry::get('config')->debug->firephp && class_exists('FirePHP') && FirePHP::getInstance() && FirePHP::getInstance()->detectClientExtension()) {
            p($msg, 'INFO');
        }
    }

}
