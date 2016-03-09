<?php
/**
 * @package Debug
 */
function p($src, $Type = 'LOG')
{
    if (!Kwf_Debug::isEnabled()) return;
    $isToDebug = false;
    if ($Type != 'ECHO' && /*Zend_Registry::get('config')->debug->firephp && */class_exists('FirePHP') && FirePHP::getInstance()) {
        if (is_object($src) && method_exists($src, 'toArray')) {
            $src = $src->toArray();
        } else if (is_object($src)) {
            $src = (array)$src;
        }
        //wenn FirePHP nicht aktiv im browser gibts false zurück
        if (FirePHP::getInstance()->fb($src, $Type)) return;
    }
    if (is_object($src) && method_exists($src, 'toDebug')) {
        $isToDebug = true;
        $src = $src->toDebug();
    }
    if (is_object($src) && method_exists($src, '__toString')) {
        $src = $src->__toString();
    }
    if (is_array($src)) {
        $isToDebug = true;
        if ($Type == 'TABLE' && php_sapi_name() != 'cli') {
            $table = '';
            $i = 0;
            foreach ($src[1] as $row) {
                $table .= "<tr>";
                foreach ($row as $td) {
                    if ($i==0) {
                        $table .= "<th>$td</th>";
                    } else {
                        $table .= "<td>$td</td>";
                    }
                }
                $table .= "</tr>\n";
                $i++;
            }
            $src = $src[0]."<br />\n<table>\n".$table."</table>\n";
        } else {
            $src = _pArray($src);
            if (php_sapi_name() == 'cli') {
                $src = "\n$src";
            } else {
                $src = "<pre>\n$src</pre>";
            }
        }
    }
    if ($isToDebug) {
        if (php_sapi_name() == 'cli') {
            $src = str_replace('<pre>', '', $src);
            $src = str_replace('</pre>', '', $src);
        }
        echo $src;
    } else if (function_exists('xdebug_var_dump')
        && !($src instanceof Zend_Db_Select ||
                $src instanceof Exception)) {
        xdebug_var_dump($src);
    } else {
        if (php_sapi_name() != 'cli') echo "<pre>";
        var_dump($src);
        if (php_sapi_name() != 'cli') echo "</pre>";
    }
    if (function_exists('debug_backtrace')) {
        $bt = debug_backtrace();
        $i = 0;
        if (isset($bt[1]) && isset($bt[1]['function']) && $bt[1]['function'] == 'd') $i = 1;
        if (isset($bt[$i]) && isset($bt[$i]['function']) && $bt[$i]['function'] == 'bt') $i++;
        echo $bt[$i]['file'].':'.$bt[$i]['line'];
        if (php_sapi_name() != 'cli') echo "<br />";
        echo "\n";
    }
}

/**
 * @internal
 */
function _pArray($src, $indent = '')
{
    $ret = '';
    if (is_array($src)) {
        $ret .= "{$indent}array ".count($src)." entries (\n";
        foreach ($src as $k=>$i) {
            $ret .= $indent."$k =>\n";
            $ret .= _pArray($i, $indent . '  ');
        }
        $ret .= "{$indent})\n";
    } else {
        if (is_object($src) && method_exists($src, 'toDebug')) {
            $src = $src->toDebug();
            $src = str_replace('<pre>', '', $src);
            $src = str_replace('</pre>', '', $src);
        } else if (is_object($src) && method_exists($src, '__toString')) {
            $src = $src->__toString();
        } else if (!is_string($src)) {
            $src = print_r($src, true);
        } else {
            if (strlen($src) > 400) {
                $src = substr($src, 0, 400)."...".' (length='.strlen($src).')';
            }
        }
        foreach (explode("\n", $src) as $l) {
            $ret .= $indent.$l."\n";
        }
    }
    return $ret;
}

/**
 * @package Debug
 */
function d($src)
{
    if (!Kwf_Debug::isEnabled()) return;
    p($src, 'ECHO');
    exit;
}

/**
 * @package Debug
 */
function pHex($s)
{
    $terminalSize = explode(' ', `stty size`);
    $breakAt = 500;
    if (isset($terminalSize[1])) {
        $breakAt = (int)($terminalSize[1]/3);
    }
    while (strlen($s) > $breakAt) {
        pHex(substr($s, 0, $breakAt));
        $s = substr($s, $breakAt);
    }
    for ($i=0;$i<strlen($s);$i++) {
        if ($s[$i] == "\0") {
            echo '\0 ';
        } else if ($s[$i] == "\n") {
            echo '\n ';
        } else if ($s[$i] == "\r") {
            echo '\r ';
        } else {
            echo $s[$i].'  ';
        }
    }
    echo "\n";
    for ($i=0;$i<strlen($s);$i++) {
        $h = dechex(ord($s[$i]));
        if (strlen($h)==1) $h = "0$h";
        echo $h.' ';
    }
    echo "\n";
}

/**
 * @package Debug
 */
class Kwf_Debug
{
    static $_enabled = 1;
    static $_view;

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) return; // error unterdrückt mit @foo()
        if (defined('E_STRICT') && $errno == E_STRICT) return;
        if (defined('E_DEPRECATED') && $errno == E_DEPRECATED) {
            if (substr($errstr, 0, 17) == 'Non-static method') {
                //ignore Non-static method called statically E_DEPRECATED errors for compatibility of older code with newer php versions
                return;
            } else if (strpos($errfile, '/usr/share/php/') !== false) {
                return;
            } else if (strpos($errstr, 'iconv_set_encoding') !== false) {
                return;
            }
        }

        //ignore notice from iconv like "Detected an incomplete multibyte character in input string"
        if ($errno == E_NOTICE && substr($errstr, 0, 15) == 'iconv_strlen():') return;

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException($exception)
    {
        if ($exception instanceof Zend_Controller_Exception && $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }
        if (!$exception instanceof Kwf_Exception_Abstract) {
            $exception = new Kwf_Exception_Other($exception);
        }
        $exception->render();
        Kwf_Benchmark::shutDown();
        Kwf_Benchmark::output();
    }

    public static function setView(Kwf_View $view)
    {
        self::$_view = $view;
    }

    public static function getView()
    {
        if (!self::$_view) self::$_view = new Kwf_View();
        return self::$_view;
    }

    public static function enable()
    {
        self::$_enabled++;
    }

    /**
     * @param int wie oft enable() aufgerufen werden muss um wirklich enabled zu sein
     */
    public static function disable($count = 1)
    {
        p('debug output disabled');
        self::$_enabled -= $count;
    }

    public static function isEnabled()
    {
        return self::$_enabled > 0;
    }


    /**
    * @internal
    */
    public static function _btString($bt)
    {
        $ret = '';
        if (isset($bt['class'])) {
            $ret .= $bt['class'].'::';
        }
        if (isset($bt['function'])) {
            $ret .= $bt['function'].'('.self::_btArgsString($bt['args']).')';
        }
        return $ret;
    }

    /**
    * @internal
    */
    public static function _btArgsString($args)
    {
        $ret = array();
        foreach ($args as $arg) {
            $ret[] = self::_btArgString($arg);
        }
        return implode(', ', $ret);
    }

    /**
    * @internal
    */
    public static function _btArgString($arg)
    {
        $ret = array();
        if ($arg instanceof Kwf_Model_Select) {
            $r = array();
            foreach ($arg->getParts() as $key =>$val) {
                $val = self::_btArgString($val);
                $r[] = "$key => $val";
            }
            $ret[] = 'select(' . implode(', ', $r) . ')';
        } else if ($arg instanceof Kwf_Component_Data) {
            $ret[] = get_class($arg).'('.$arg->componentId.')';
        } else if (is_object($arg)) {
            $ret[] = get_class($arg);
        } else if (is_array($arg)) {
            $arrayString = array();
            foreach ($arg as $k=>$i) {
                $i = self::_btArgString($i);
                if (!is_int($k)) {
                    $arrayString[] = "$k => $i";
                } else {
                    $arrayString[] = $i;
                }
            }
            $ret[] = 'array('.implode(', ', $arrayString).')';
        } else if (is_null($arg)) {
            $ret[] = 'null';
        } else if (is_string($arg)) {
            if (strlen($arg) > 200) $arg = substr($arg, 0, 197)."...";
            $ret[] = '"'.$arg.'"';
        } else if (is_bool($arg)) {
            $ret[] = $arg ? 'true' : 'false';
        } else {
            $ret[] = $arg;
        }
        return current($ret);
    }

    public static function btString()
    {
        $bt = debug_backtrace();
        $ret = '';
        foreach ($bt as $i) {
            if (isset($i['file']) && substr($i['file'], 0, 22) == '/usr/share/php/PHPUnit') break;
            if (isset($i['file']) && substr($i['file'], 0, 16) == '/usr/bin/phpunit') break;
            if (isset($i['file']) && substr($i['file'], 0, 16) == '/www/public/niko/phpunit') break;
            $ret .=
                (isset($i['file']) ? $i['file'] : 'Unknown file') . ':' .
                (isset($i['line']) ? $i['line'] : '?') . ' - ' .
                ((isset($i['object']) && $i['object'] instanceof Kwf_Component_Data) ? $i['object']->componentId . '->' : '') .
                (isset($i['function']) ? $i['function'] : '') . '(' .
                self::_btArgsString($i['args']) . ')' . "\n";
        }
        $ret .= "\n";
        return $ret;
    }

    public static function bt($file = false)
    {
        if (!Kwf_Debug::isEnabled()) return;
        if (php_sapi_name() == 'cli' || $file) {
            $ret = self::btString();
            if ($file) {
                $ret = str_repeat("=", 45)."\n".
                    php_sapi_name().' '.(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '').
                    "\n".$ret;
                file_put_contents('backtrace', $ret, FILE_APPEND);
            } else {
                echo $ret;
            }
        } else {
            $bt = debug_backtrace();
            unset($bt[0]);
            $out = array(array('File', 'Line', 'Function', 'Args'));
            foreach ($bt as $i) {
                $out[] = array(
                    isset($i['file']) ? $i['file'] : '', isset($i['line']) ? $i['line'] : '',
                    isset($i['function']) ? $i['function'] : null,
                    self::_btArgsString($i['args']),
                );
            }
            p(array('Backtrace for '.self::_btString($bt[1]), $out), 'TABLE');
        }
    }

}
