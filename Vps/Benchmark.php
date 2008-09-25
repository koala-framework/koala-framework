<?php
class Vps_Benchmark
{
    private static $_startTime;
    private static $_enabled = false;
    private static $_logEnabled = false;
    private static $_counter = array();
    public static $benchmarks = array();

    /**
     * Startet eine Sequenz
     *
     * @param string $identifier
     */
    public static function start($identifier = null, $addInfo = null)
    {
        if (!self::$_enabled) return null;
        return new Vps_Benchmark_Profile($identifier, $addInfo);
    }

    public static function enable()
    {
        if (!isset(self::$_startTime)) self::$_startTime = microtime(true);
        self::$_enabled = true;
    }

    public static function disable()
    {
        self::$_enabled = false;
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

    public static function reset()
    {
        self::$_counter = array();
    }
    public static function getCounterValue($name)
    {
        $ret = self::$_counter[$name];
        if (is_array($ret)) $ret = count($ret);
        return $ret;
    }
    public static function count($name, $value = null)
    {
        if (!self::$_enabled && !self::$_logEnabled) return false;

        self::_countArray(self::$_counter, $name, $value);

        foreach (self::$benchmarks as $b) {
            if (!$b->stopped) {
                self::_countArray($b->counter, $name, $value);
            }
        }
    }
    private static function _countArray(&$counter, $name, $value)
    {
        if (!isset($counter[$name])) {
            if ($value) {
                $counter[$name] = array();
            } else {
                $counter[$name] = 0;
            }
        }
        if ($value) {
            if (!is_array($counter[$name])) {
                throw new Vps_Exception("Missing value for counter '$name'");
            }
            $counter[$name][] = $value;;
        } else {
            if (is_array($counter[$name])) {
                throw new Vps_Exception("no value possible for counter '$name'");
            }
            $counter[$name]++;
        }
    }

    public static function output()
    {
        if (!self::$_enabled) return;
        if (PHP_SAPI != 'cli') {
            echo '<div style="text-align:left;position:absolute;top:0;right:0;z-index:1;width:200px">';
            echo '<div style="font-family:Verdana;font-size:10px;background-color:white;width:1500px;position:absolute;padding:5px;">';
            echo round(microtime(true) - self::$_startTime, 2)." sec<br />\n";
            $load = @file_get_contents('/proc/loadavg');
            $load = explode(' ', $load);
            echo "Load: ". $load[0]."<br />\n";
            echo "Memory: ".round(memory_get_peak_usage()/1024)." kb<br />\n";
            if (Zend_Registry::get('db')->getProfiler() && method_exists(Zend_Registry::get('db')->getProfiler(), 'getQueryCount')) {
                echo "DB-Queries: ".Zend_Registry::get('db')->getProfiler()->getQueryCount()."<br />\n";
            }
        }
        self::_outputCounter(self::$_counter);
        if (self::$benchmarks) {
            echo "<br /><b>Benchmarks:</b><br/>";
            foreach (self::$benchmarks as $i) {
                echo "<a style=\"display:block;\"href=\"#\" onclick=\"if(this.nextSibling.nextSibling.style.display=='none') { this.open=true; this.nextSibling.nextSibling.style.display='block'; this.nextSibling.style.display=''; } else { this.open=false; this.nextSibling.nextSibling.style.display='none';this.nextSibling.style.display='none'; } return(false); }\"
                                                            onmouseover=\"if(!this.open) this.nextSibling.style.display=''\"
                                                            onmouseout=\"if(!this.open) this.nextSibling.style.display='none'\">";
                echo "{$i->identifier} (".round($i->duration, 3)." sec)</a>";
                echo "<div style=\"display:none;margin-left:10px\">";
                echo $i->addInfo."<br/>";
                echo implode('<br />', $i->getOutput());
                echo "</div>";
                echo "<div style=\"display:none;margin-left:10px\">";
                self::_outputCounter($i->counter);
                echo "</div>";
            }
        }
        if (PHP_SAPI != 'cli') {
            echo "</div>";
            echo "</div>";
        }
    }
    private static function _outputCounter($counter)
    {
        echo "\n";
        foreach ($counter as $k=>$i) {
            if (is_array($i)) {
                if (PHP_SAPI != 'cli') {
                    echo "<a style=\"display:block;\"href=\"#\" onclick=\"if(this.nextSibling.style.display=='none') this.nextSibling.style.display='block'; else this.nextSibling.style.display='none';return(false);\">";
                    echo "$k: ".count($i)."</a>";
                    echo "<ul style=\"display:none\">";
                    foreach ($i as $j) {
                        echo "<li>$j</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "$k: (".count($i).') ';
                    foreach ($i as $j) {
                        echo $j.' ';
                    }
                    echo "\n";
                }
            } else {
                if (PHP_SAPI != 'cli') {
                    echo "$k: $i<br />\n";
                } else {
                    echo "$k: $i\n";
                }
            }
        }
    }

    public static function info($msg)
    {
        if (!self::$_enabled) return;
        if (Zend_Registry::get('config')->debug->firephp && class_exists('FirePHP') && FirePHP::getInstance() && FirePHP::getInstance()->detectClientExtension()) {
            p($msg, 'INFO');
        }
    }

    public static function shutDown()
    {
        if (!self::$_logEnabled) return;
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
            $fields = array('date', 'url', 'ip', 'useragent', 'load', 'duration', 'memory', 'queries');
            foreach (self::$_counter as $k=>$i) {
                $fields[] = $k;
            }
        }
        $fp = fopen('benchmark', $newFile ? 'w' : 'a');
        if ($newFile) {
            fwrite($fp, implode(';', $fields)."\n");
        }
        $out = array();
        foreach ($fields as $i) {
            if ($i == 'ip') {
                $out[] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            } else if ($i == 'useragent') {
                $out[] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            } else if ($i == 'date') {
                $out[] = date('Y-m-d H:i:s');
            } else if ($i == 'url') {
                $out[] = $_SERVER['REQUEST_URI'];
            } else if ($i == 'load') {
                $load = @file_get_contents('/proc/loadavg');
                $load = explode(' ', $load);
                if (isset($load[0])) {
                    $out[] = $load[0];
                } else {
                    $out[] = '';
                }
            } else if ($i == 'duration') {
                $out[] = round(microtime(true) - self::$_startTime, 2);
            } else if ($i == 'memory') {
                $out[] = round(memory_get_peak_usage()/1024);
            } else if ($i == 'queries') {
                //$out[] = Vps_Db_Profiler::getCount();
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
    }

}
