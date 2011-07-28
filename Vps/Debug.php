<?php
function p($src, $Type = 'LOG')
{
    if (!Vps_Debug::isEnabled()) return;
    $isToDebug = false;
    if ($Type != 'ECHO' && Zend_Registry::get('config')->debug->firephp && class_exists('FirePHP') && FirePHP::getInstance()) {
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
    if ($isToDebug) {
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
        echo $bt[$i]['file'].':'.$bt[$i]['line'];
        if (php_sapi_name() != 'cli') echo "<br />";
        echo "\n";
    }
}

function d($src)
{
    if (!Vps_Debug::isEnabled()) return;
    p($src, 'ECHO');
    exit;
}

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
    for($i=0;$i<strlen($s);$i++) {
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
    for($i=0;$i<strlen($s);$i++) {
        $h = dechex(ord($s[$i]));
        if (strlen($h)==1) $h = "0$h";
        echo $h.' ';
    }
    echo "\n";
}

function _btString($bt)
{
    $ret = '';
    if (isset($bt['class'])) {
        $ret .= $bt['class'].'::';
    }
    if (isset($bt['function'])) {
        $ret .= $bt['function'].'('._btArgsString($bt['args']).')';
    }
    return $ret;
}
function _btArgsString($args)
{
    $ret = array();
    foreach ($args as $arg) {
        $ret[] = _btArgString($arg);
    }
    return implode(', ', $ret);
}
function _btArgString($arg)
{
    $ret = array();
    if ($arg instanceof Vps_Model_Select) {
        $r = array();
        foreach ($arg->getParts() as $key =>$val) {
            $val = _btArgString($val);
            $r[] = "$key => $val";
        }
        $ret[] = 'select(' . implode(', ', $r) . ')';
    } else if ($arg instanceof Vps_Component_Data) {
        $ret[] = get_class($arg).'('.$arg->componentId.')';
    } else if (is_object($arg)) {
        $ret[] = get_class($arg);
    } else if (is_array($arg)) {
        $arrayString = array();
        foreach ($arg as $k=>$i) {
            $i = _btArgString($i);
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
        if (strlen($arg) > 50) $arg = substr($arg, 0, 47)."...";
        $ret[] = '"'.$arg.'"';
    } else if (is_bool($arg)) {
        $ret[] = $arg ? 'true' : 'false';
    } else {
        $ret[] = $arg;
    }
    return current($ret);
}
function bt($file = false, $return = false)
{
    if (!Vps_Debug::isEnabled()) return;
    $bt = debug_backtrace();
    if (php_sapi_name() == 'cli' || $file) {
        $ret = '';
        foreach ($bt as $i) {
            if (isset($i['file']) && substr($i['file'], 0, 22) == '/usr/share/php/PHPUnit') break;
            if (isset($i['file']) && substr($i['file'], 0, 16) == '/usr/bin/phpunit') break;
            if (isset($i['file']) && substr($i['file'], 0, 16) == '/www/public/niko/phpunit') break;
            $ret .=
                (isset($i['file']) ? $i['file'] : 'Unknown file') . ':' .
                (isset($i['line']) ? $i['line'] : '?') . ' - ' .
                ((isset($i['object']) && $i['object'] instanceof Vps_Component_Data) ? $i['object']->componentId . '->' : '') .
                (isset($i['function']) ? $i['function'] : '') . '(' .
                _btArgsString($i['args']) . ')' . "\n";
        }
        $ret .= "\n";
        if ($return) {
            return $ret;
        } else if ($file) {
            $ret = "=============================================\n\n".$ret;
            file_put_contents('backtrace', $ret, FILE_APPEND);
        } else {
            echo $ret;
        }
    } else {
        unset($bt[0]);
        $out = array(array('File', 'Line', 'Function', 'Args'));
        foreach ($bt as $i) {
            $out[] = array(
                isset($i['file']) ? $i['file'] : '', isset($i['line']) ? $i['line'] : '',
                isset($i['function']) ? $i['function'] : null,
                _btArgsString($i['args']),
            );
        }
        p(array('Backtrace for '._btString($bt[1]), $out), 'TABLE');
    }
}

class Vps_Debug
{
    static $_enabled = true;
    static $_view;

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() == 0) return; // error unterdrückt mit @foo()
        if (defined('E_DEPRECATED') && $errno == E_DEPRECATED
            && (strpos($errfile, 'tcpdf/') !== false || strpos($errfile, '/usr/share/php/') !== false)) {
            return;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException($exception, $ignoreCli = false)
    {
        if (!$exception instanceof Vps_Exception_Abstract) {
            $exception = new Vps_Exception_Other($exception);
        }

        if (!$ignoreCli && php_sapi_name() == 'cli') {
            $exception->log();
            file_put_contents('php://stderr', $exception->getException()->__toString()."\n");
            exit(1);
        }

        $view = self::getView();
        $view->exception = $exception->getException();
        $view->message = $exception->getException()->getMessage();
        $view->requestUri = isset($_SERVER['REQUEST_URI']) ?
            $_SERVER['REQUEST_URI'] : '' ;
        $view->debug = Vps_Exception::isDebug();

        $header = $exception->getHeader();
        $template = $exception->getTemplate();
        $template = strtolower(Zend_Filter::filterStatic($template, 'Word_CamelCaseToDash').'.tpl');
        if ($exception instanceof Vps_Exception_Abstract) $exception->log();

        if (!headers_sent()) header($header);
        try {
            echo $view->render($template);
        } catch (Exception $e) {
            echo '<pre>';
            echo $exception->getException()->__toString();
            echo "\n\n\nError happened while handling exception:";
            echo $e->__toString();
            echo '</pre>';
        }
        Vps_Benchmark::shutDown();
        Vps_Benchmark::output();
    }

    public static function setView(Vps_View $view)
    {
        self::$_view = $view;
    }

    public static function getView()
    {
        if (!self::$_view) self::$_view = new Vps_View();
        return self::$_view;
    }

    public static function enable()
    {
        self::$_enabled = true;
    }

    public static function disable()
    {
        p('debug output disabled');
        self::$_enabled = false;
    }

    public static function isEnabled()
    {
        return self::$_enabled;
    }
}
