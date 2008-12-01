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
        if (isset($_COOKIE['unitTest'])) return;
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
        if (!is_array($counter)) return;
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

        static $wasCalled = false;
        if ($wasCalled) return;
        $wasCalled = true;

        $memcache = new Memcache;
        $memcache->addServer('localhost');
        $prefix = Zend_Registry::get('config')->application->id.'-'.
                            Vps_Setup::getConfigSection().'-bench-';
        if (!isset($_SERVER['REQUEST_URI'])) {
            if (php_sapi_name() == 'cli') $urlType = 'cli';
            else $urlType = 'unknown';
        } else if (substr($_SERVER['REQUEST_URI'], 0, 8) == '/assets/') {
            $urlType = 'asset';
        } else if (substr($_SERVER['REQUEST_URI'], 0, 7) == '/media/') {
            $urlType = 'media';
        } else if (substr($_SERVER['REQUEST_URI'], 0, 7) == '/admin/') {
            $urlType = 'admin';
        } else if (substr($_SERVER['REQUEST_URI'], 0, 5) == '/vps/') {
            $urlType = 'admin';
        } else {
            $urlType = 'content';
        }
        $prefix .= $urlType.'-';
        if (!$memcache->increment($prefix.'requests', 1)) {
            $memcache->set($prefix.'requests', 1, 0, 0);
        }
        foreach (self::$_counter as $name=>$value) {
            if (is_array($value)) $value = count($value);
            if (!$memcache->increment($prefix.$name, $value)) {
                $memcache->set($prefix.$name, $value, 0, 0);
            }
        }
        $value = (int)((microtime(true) - self::$_startTime)*1000);
        if (!$memcache->increment($prefix.'duration', $value)) {
            $memcache->set($prefix.'duration', $value, 0, 0);
        }
    }

}
