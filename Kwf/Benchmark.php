<?php
class Kwf_Benchmark
{
    private static $_enabled = false;
    private static $_logEnabled = false;
    protected static $_counter = array();
    protected static $_counterLog = array();
    public static $benchmarks = array();
    private static $_checkpoints = array();
    private static $_subCheckpoints = array();
    public static $startTime; //wird von Kwf_Setup::setUp gesetzt

    private static function _getInstance()
    {
        static $i;
        if (!isset($i)) {
            $c = Kwf_Config::getValue('benchmarkClass');
            if (!class_exists($c)) {
                $c = 'Kwf_Benchmark';
            }
            $i = new $c();
        }
        return $i;
    }

    /**
     * Startet eine Sequenz
     *
     * @param string $identifier
     */
    public static function start($identifier = null, $addInfo = null)
    {
        if (!self::$_enabled) return null;
        return new Kwf_Benchmark_Profile($identifier, $addInfo);
    }

    public static function enable()
    {
        self::$_enabled = true;
    }

    public static function disable()
    {
        self::$_enabled = false;
    }

    public static function enableLog()
    {
        self::$_logEnabled = true;
    }

    public static function isLogEnabled()
    {
        return self::$_logEnabled;
    }

    public static function isEnabled()
    {
        return self::$_enabled;
    }

    public static function reset()
    {
        self::$_counter = array();
        self::$_counterLog = array();
        self::$_checkpoints = array();
        self::$_subCheckpoints = array();
        self::$startTime = microtime(true);
    }
    public static function getCounterValue($name)
    {
        if (!isset(self::$_counter[$name])) return null;
        $ret = self::$_counter[$name];
        if (is_array($ret)) $ret = count($ret);
        return $ret;
    }
    public static function count($name, $value = null)
    {
        if (!self::$_enabled) return false;

        self::_countArray(self::$_counter, $name, $value);

        foreach (self::$benchmarks as $b) {
            if (!$b->stopped) {
                self::_countArray($b->counter, $name, $value);
            }
        }
    }

    public static function countBt($name, $value = null)
    {
        if (!self::$_enabled) return false;

        self::_countArray(self::$_counter, $name, $value, true);

        foreach (self::$benchmarks as $b) {
            if (!$b->stopped) {
                self::_countArray($b->counter, $name, $value, true);
            }
        }
    }

    public static function countLog($name)
    {
        if (!self::$_logEnabled) return;

        self::_countArray(self::$_counterLog, $name, null);
    }

    private static function _countArray(&$counter, $name, $value, $backtrace = false)
    {
        if (!isset($counter[$name])) {
            if (!is_null($value)) {
                $counter[$name] = array();
            } else {
                $counter[$name] = 0;
            }
        }
        if (!is_null($value)) {
            if (!is_array($counter[$name])) {
                throw new Kwf_Exception("Missing value for counter '$name'");
            }
            $bt = false;
            if ($backtrace) {
                $b = debug_backtrace();
                unset($b[0]);
                unset($b[1]);
                $bt = '';
                foreach ($b as $i) {
                    $bt .=
                        (isset($i['file']) ? $i['file'] : 'Unknown file') . ':' .
                        (isset($i['line']) ? $i['line'] : '?') . ' - ' .
                        ((isset($i['object']) && $i['object'] instanceof Kwf_Component_Data) ? $i['object']->componentId . '->' : '') .
                        (isset($i['function']) ? $i['function'] : '') . '(' .
                        _btArgsString($i['args']) . ')' . "<br />";
                }
            }
            $counter[$name][] = array(
                'value' => $value,
                'bt' => $bt
            );
        } else {
            if (is_array($counter[$name])) {
                throw new Kwf_Exception("no value possible for counter '$name'");
            }
            $counter[$name]++;
        }
    }

    public static function output()
    {
        self::shutDown();

        if (!self::$_enabled) return;
        self::disable();
        self::$_logEnabled = false;

        $execTime = microtime(true) - self::$startTime;
        if (function_exists('memory_get_peak_usage')) {
            $memoryUsage = memory_get_peak_usage();
        } else {
            $memoryUsage = memory_get_usage();
        }
        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load);
        $load = $load[0];

        $benchmarkOutput = array();
        if ($execTime < 1) {
            $benchmarkOutput[] = round($execTime*1000)." msec";
        } else {
            $benchmarkOutput[] = round($execTime, 3)." sec";
        }
        if ($load) $benchmarkOutput[] = "Load: ". $load;
        $benchmarkOutput[] = "Memory: ".round($memoryUsage/1024)." kb";
        if (Zend_Registry::get('dao') && Zend_Registry::get('dao')->hasDb() && Zend_Registry::get('db')) {
            if (Zend_Registry::get('db')->getProfiler() && method_exists(Zend_Registry::get('db')->getProfiler(), 'getQueryCount')) {
                $benchmarkOutput[] = "DB-Queries: ".Zend_Registry::get('db')->getProfiler()->getQueryCount();
            } else {
                $benchmarkOutput[] = "DB-Queries: (no profiler used)";
            }
        } else {
            $benchmarkOutput[] = "DB-Queries: (none)";
        }
        if (PHP_SAPI != 'cli' && (Kwf_Config::getValue('debug.benchmark') || isset($_REQUEST['KWF_BENCHMARK']))) {
            echo '<div class="outerBenchmarkBox">';
            echo '<div class="innerBenchmarkBox">';
            foreach ($benchmarkOutput as $line) {
                echo "$line<br />\n";
            }
        }
        if (Kwf_Config::getValue('debug.benchmarklog')) {
            $out = date('Y-m-d H:i:s')."\n";
            $out .= Kwf_Setup::getRequestPath()."\n";
            $out .= implode("\n", $benchmarkOutput)."\n";
            file_put_contents('benchmarklog', $out);
        }

        if ((Kwf_Config::getValue('debug.benchmark') || isset($_REQUEST['KWF_BENCHMARK'])) && PHP_SAPI != 'cli') {
            echo self::_getCounterOutput(self::$_counter, true);
            if (self::$benchmarks) {
                echo "<br /><b>Benchmarks:</b><br/>";
                foreach (self::$benchmarks as $i) {
                    echo "<a style=\"display:block;\" href=\"#\" onclick=\"if(this.nextSibling.nextSibling.style.display=='none') { this.open=true; this.nextSibling.nextSibling.style.display='block'; this.nextSibling.style.display=''; } else { this.open=false; this.nextSibling.nextSibling.style.display='none';this.nextSibling.style.display='none'; } return(false); }\"
                                                                onmouseover=\"if(!this.open) this.nextSibling.style.display=''\"
                                                                onmouseout=\"if(!this.open) this.nextSibling.style.display='none'\">";
                    echo "{$i->identifier} (".round($i->duration, 3)." sec)</a>";
                    echo "<div style=\"display:none;margin-left:10px\">";
                    echo $i->addInfo."<br/>";
                    echo implode('<br />', $i->getOutput());
                    echo "</div>";
                    echo "<div style=\"display:none;margin-left:10px\">";
                    echo self::_getCounterOutput($i->counter, true);
                    echo "</div>";
                }
            }
        }

        if (Kwf_Config::getValue('debug.benchmarklog')) {
            $out = self::_getCounterOutput(self::$_counter, false);
            if (self::$benchmarks) {
                $out .= "\nBenchmarks:\n";
                foreach (self::$benchmarks as $i) {
                    $out .= "{$i->identifier} (".round($i->duration, 3)." sec)\n";
                    $out .= "    ".$i->addInfo."\n";
                    $out .= implode("\n    ", $i->getOutput());
                    $out .= self::_getCounterOutput($i->counter, true);
                    $out .= "\n";
                }
            }
            file_put_contents('benchmarklog', $out, FILE_APPEND);
        }


        if ((Kwf_Config::getValue('debug.benchmark') || isset($_REQUEST['KWF_BENCHMARK'])) && PHP_SAPI != 'cli') {
            echo "<table style=\"font-size: 10px\">";
            echo "<tr><th>ms</th><th>%</th><th>Checkpoint</th></tr>";
            $sum = 0;
            foreach (self::$_checkpoints as $checkpoint) {
                $sum += $checkpoint[0];
            }
            foreach (self::$_checkpoints as $i=>$checkpoint) {
                echo "<tr>";
                echo "<td>".round($checkpoint[0]*1000)."</td>";
                echo "<td>".round(($checkpoint[0]/$sum)*100)."</td>";
                echo "<td>".$checkpoint[1]."</td>";
                echo "</tr>";
                if (isset(self::$_subCheckpoints[$i])) {
                    $subCheckpoints = array();
                    foreach (self::$_subCheckpoints[$i] as $cp) {
                        $subCheckpoints[0][] = $cp[0];
                        $subCheckpoints[1][] = $cp[1];
                    }
                    array_multisort($subCheckpoints[0], SORT_DESC, SORT_NUMERIC, $subCheckpoints[1]);
                    foreach (array_keys($subCheckpoints[0]) as $k) {
                        $percent = ($subCheckpoints[0][$k]/$sum)*100;
                        if ($percent > 1) {
                            echo "<tr>";
                            echo "<td>".round($subCheckpoints[0][$k]*1000)."</td>";
                            echo "<td>".round($percent)."</td>";
                            echo "<td>&nbsp;&nbsp;".$subCheckpoints[1][$k]."</td>";
                            echo "</tr>";
                        }
                    }
                }
            }
            echo "</table>";
            echo "</div>";
            echo "</div>";
        }
        if (Kwf_Config::getValue('debug.benchmarklog')) {
            $out = "\n".self::getCheckpointOutput();
            file_put_contents('benchmarklog', $out, FILE_APPEND);
        }
    }

    public static function getCheckpointOutput()
    {
        $out = "  ms  % Checkpoint\n";
        $sum = 0;
        foreach (self::$_checkpoints as $checkpoint) {
            $sum += $checkpoint[0];
        }
        foreach (self::$_checkpoints as $i=>$checkpoint) {
            $ms = round($checkpoint[0]*1000);
            $out .= str_pad(round($checkpoint[0]*1000), 4, ' ', STR_PAD_LEFT);
            $out .= str_pad(round(($checkpoint[0]/$sum)*100), 3, ' ', STR_PAD_LEFT);
            $out .= " ".$checkpoint[1]."";
            $out .= "\n";
            if (isset(self::$_subCheckpoints[$i])) {
                $subCheckpoints = array();
                foreach (self::$_subCheckpoints[$i] as $cp) {
                    $subCheckpoints[0][] = $cp[0];
                    $subCheckpoints[1][] = $cp[1];
                }
                array_multisort($subCheckpoints[0], SORT_DESC, SORT_NUMERIC, $subCheckpoints[1]);
                foreach (array_keys($subCheckpoints[0]) as $k) {
                    $percent = ($subCheckpoints[0][$k]/$sum)*100;
                    if ($percent > 1) {
                        $out .= ' '.str_pad(round($subCheckpoints[0][$k]*1000), 4, ' ', STR_PAD_LEFT);
                        $out .= str_pad(round($percent), 3, ' ', STR_PAD_LEFT);
                        $out .= '  '.$subCheckpoints[1][$k];
                        $out .= "\n";
                    }
                }
            }
        }
        return $out;
    }

    private static function _getCounterOutput($counter, $useHtml)
    {
        $ret = "\n";
        if (!is_array($counter)) return;
        foreach ($counter as $k=>$i) {
            if (is_array($i)) {
                if ($useHtml) {
                    $ret .= "<a style=\"display:block;\" href=\"#\" onclick=\"if(this.nextSibling.style.display=='none') this.nextSibling.style.display='block'; else this.nextSibling.style.display='none';return(false);\">";
                    $ret .= "$k: ".count($i)."</a>";
                    $ret .= "<ul style=\"display:none\">";
                    foreach ($i as $j) {
                        $ret .= "<li>";
                        if ($j['bt']) {
                            $ret .= "<strong style=\"font-weight:bold\">{$j['value']}</strong><br />{$j['bt']}";
                        } else {
                            $ret .= $j['value'];
                        }
                        $ret .= "</li>";
                    }
                    $ret .= "</ul>";
                } else {
                    $ret .= "$k: (".count($i).') ';
                    $len = '';
                    foreach ($i as $j) {
                        $v = $j['value'].' ';
                        $len += strlen($v);
                        if ($len > 1000) {
                            $ret .= "\n    ";
                            $len = 0;
                        }
                        $ret .= $v;
                    }
                    $ret = trim($ret);
                    $ret .= "\n";
                }
            } else {
                if ($useHtml) {
                    $ret .= "$k: $i<br />\n";
                } else {
                    $ret .= "$k: $i\n";
                }
            }
        }
        return $ret;
    }

    final public static function shutDown()
    {
        static $wasCalled = false;
        if ($wasCalled) return;
        $wasCalled = true;

        Kwf_Benchmark::checkpoint('shutDown');

        if (!self::$_logEnabled) return;

        self::_getInstance()->_shutDown();
    }

    final public static function getUrlType()
    {
        return self::_getInstance()->_getUrlType();
    }

    protected function _getUrlType()
    {
        $prefixLen = strlen(Kwf_Setup::getBaseUrl());
        if (!isset($_SERVER['REQUEST_URI'])) {
            if (php_sapi_name() == 'cli') $urlType = 'cli';
            else $urlType = 'unknown';
        } else if (substr($_SERVER['REQUEST_URI'], $prefixLen, 8) == '/assets/') {
            $urlType = 'asset';
        } else if (substr($_SERVER['REQUEST_URI'], $prefixLen, 7) == '/media/') {
            $urlType = 'media';
        } else if (substr($_SERVER['REQUEST_URI'], $prefixLen, 7) == '/admin/') {
            $urlType = 'admin';
        } else if (substr($_SERVER['REQUEST_URI'], $prefixLen, 5) == '/kwf/') {
            $urlType = 'admin';
        } else {
            $urlType = 'content';
        }
        return $urlType;
    }

    protected function _shutDown()
    {
        $urlType = $this->_getUrlType();
        if ($urlType == 'asset' && !self::$_counterLog) return;
        $prefix = $urlType.'-';
        $this->_memcacheCount($prefix.'requests', 1);
        foreach (self::$_counterLog as $name=>$value) {
            $this->_memcacheCount($name, $value);
        }
    }

    private function _memcacheCount($name, $value)
    {
        Kwf_Benchmark_Counter::getInstance()->increment($name, $value);
    }

    public static function memcacheCount($name, $value = 1)
    {
        self::_getInstance()->_memcacheCount($name, $value);
    }

    public static function subCheckpoint($description, $time)
    {
        if (!self::$_enabled) return;
        if (!isset(self::$_subCheckpoints[count(self::$_checkpoints)])) {
            self::$_subCheckpoints[count(self::$_checkpoints)] = array();
        }
        self::$_subCheckpoints[count(self::$_checkpoints)][] = array(
            $time,
            $description,
        );
    }

    public static function checkpoint($description)
    {
        if (!self::$_enabled) return;
        static $previousTime;
        if (!$previousTime) $previousTime = self::$startTime;
        $time = microtime(true) - $previousTime;
        $previousTime = microtime(true);
        self::$_checkpoints[] = array(
            $time,
            $description,
            array() //sub
        );
    }
}
