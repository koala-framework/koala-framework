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
    $breakAt = 1000;
    if (isset($terminalSize[1])) {
        $breakAt = (int)($terminalSize[1]/3);
    }
    while (strlen($s) > $breakAt) {
        dmp(substr($s, 0, $breakAt));
        $s = substr($s, $breakAt);
    }
    for($i=0;$i<strlen($s);$i++) {
        echo $s[$i].'  ';
        if ($s[$i] == "\0") echo " ";
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
function bt($file = false)
{
    if (!Vps_Debug::isEnabled()) return;
    $bt = debug_backtrace();
    unset($bt[0]);
    if (php_sapi_name() == 'cli' || $file) {
        $ret = '';
        foreach ($bt as $i) {
            if (isset($i['file']) && substr($i['file'], 0, 22) == '/usr/share/php/PHPUnit') continue;
            if (isset($i['file']) && substr($i['file'], 0, 16) == '/usr/bin/phpunit') continue;
            $ret .=
                (isset($i['file']) ? $i['file'] : 'Unknown file') . ':' .
                (isset($i['line']) ? $i['line'] : '?') . ' - ' .
                ((isset($i['object']) && $i['object'] instanceof Vps_Component_Data) ? $i['object']->componentId . '->' : '') .
                (isset($i['function']) ? $i['function'] : '') . '(' .
                _btArgsString($i['args']) . ')' . "\n";
        }
        $ret .= "\n";
        if ($file) {
            $ret = "=============================================\n\n".$ret;
            file_put_contents('backtrace', $ret, FILE_APPEND);
        } else {
            echo $ret;
        }
    } else {
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

function hlp($string){
    return Zend_Registry::get('hlp')->hlp($string);
}

function hlpVps($string){
    return Zend_Registry::get('hlp')->hlpVps($string);
}

function trl($string, $text = array())
{
    return Zend_Registry::get('trl')->trl($string, $text, Vps_Trl::SOURCE_WEB);
}

function trlc($context, $string, $text = array()) {
    return Zend_Registry::get('trl')->trlc($context, $string, $text, Vps_Trl::SOURCE_WEB);
}

function trlp($single, $plural, $text =  array()) {
    return Zend_Registry::get('trl')->trlp($single, $plural, $text, Vps_Trl::SOURCE_WEB);
}

function trlcp($context, $single, $plural = null, $text = array()){
    return Zend_Registry::get('trl')->trlcp($context, $single, $plural, $text, Vps_Trl::SOURCE_WEB);
}

function trlVps($string, $text = array()){
    return Zend_Registry::get('trl')->trl($string, $text, Vps_Trl::SOURCE_VPS);
}

function trlcVps($context, $string, $text = array()){
    return Zend_Registry::get('trl')->trlc($context, $string, $text, Vps_Trl::SOURCE_VPS);
}

function trlpVps($single, $plural, $text =  array()){
    return Zend_Registry::get('trl')->trlp($single, $plural, $text, Vps_Trl::SOURCE_VPS);
}

function trlcpVps($context, $single, $plural, $text = array()){
    return Zend_Registry::get('trl')->trlcp($context, $single, $plural, $text, Vps_Trl::SOURCE_VPS);
}

//instanceof operator geht für strings ned korrekt, von php.net gfladad
function is_instance_of($sub, $super)
{
    $sub = is_object($sub) ? get_class($sub) : (string)$sub;
    $super = is_object($super) ? get_class($super) : (string)$super;
    Zend_Loader::loadClass($sub);
    Zend_Loader::loadClass($super);

    switch(true)
    {
        case $sub === $super:
        case is_subclass_of($sub, $super):
        case in_array($super, class_implements($sub)):
            return true;
        default:
            return false;
    }
}

class Vps_Setup
{
    public static $configClass;
    public static function setUp($configClass = 'Vps_Config_Web')
    {
        require_once 'Vps/Loader.php';
        if (isset($_SERVER['REQUEST_URI']) &&
            substr($_SERVER['REQUEST_URI'], 0, 25) == '/vps/json-progress-status' &&
            !empty($_REQUEST['progressNum'])
        ) {
            Vps_Loader::registerAutoload();
            $pbarAdapter = new Vps_Util_ProgressBar_Adapter_Cache($_REQUEST['progressNum']);
            $pbarStatus = $pbarAdapter->getStatus();
            if (!$pbarStatus) {
                $pbarStatus = array();
            }
            $pbarStatus['success'] = true;
            echo Zend_Json::encode($pbarStatus);
            exit;
        }
        require_once 'Vps/Registry.php';

        Zend_Registry::setClassName('Vps_Registry');

        self::$configClass = $configClass;
        require_once 'Vps/Config/Web.php';
        Vps_Registry::set('configMtime', Vps_Config_Web::getInstanceMtime(self::getConfigSection()));
        $config = Vps_Config_Web::getInstance(self::getConfigSection());
        Vps_Registry::set('config', $config);


        if ($config->debug->benchmark) {
            require_once 'Vps/Benchmark.php';
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            Vps_Benchmark::enable();
        }
        if ($config->debug->benchmarkLog) {
            require_once 'Vps/Benchmark.php';
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            Vps_Benchmark::enableLog();
        }
        Vps_Loader::registerAutoload();

        ini_set('memory_limit', '128M');
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        mb_internal_encoding('UTF-8');
        set_error_handler(array('Vps_Debug', 'handleError'), E_ALL);
        set_exception_handler(array('Vps_Debug', 'handleException'));
        umask(002);

        $ip = get_include_path();
        foreach ($config->includepath as $p) {
            $ip .= PATH_SEPARATOR . $p;
        }
        set_include_path($ip);

        Zend_Registry::set('requestNum', ''.floor(microtime(true)*100));

        if ($config->debug->firephp && php_sapi_name() != 'cli') {
            require_once 'FirePHPCore/FirePHP.class.php';
            FirePHP::init();
        }

        if ($config->debug->querylog && php_sapi_name() != 'cli') {
            header('X-Vps-RequestNum: '.Zend_Registry::get('requestNum'));
            register_shutdown_function(array('Vps_Setup', 'shutDown'));
        }
        if (($config->debug->firephp || $config->debug->querylog)
                && php_sapi_name() != 'cli')
        {
            ob_start();
        }

        if (isset($_POST['PHPSESSID'])) {
            //für swfupload
            Zend_Session::setId($_POST['PHPSESSID']);
        }

        if (isset($_COOKIE['unitTest'])) {
            $config->debug->benchmark = false;
        }

        // Falls redirectToDomain eingeschalten ist, umleiten
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        if ($host && $config->server->redirectToDomain) {
            $redirect = false;
            if ($config->vpc->domains) {
                $domains = $config->vpc->domains;
                $noRedirect = false;
                foreach ($domains as $domain) {
                    if ($domain->domain == $host) {
                        $noRedirect = true;
                        break;
                    }
                }
                if (!$noRedirect) {
                    foreach ($domains as $domain) {
                        if (!$redirect && !$domain->pattern) $redirect = $domain->domain;
                        if ($domain->pattern && preg_match('/' . $domain->pattern . '/', $host)
                        ) {
                            $redirect = $domain->domain;
                            break;
                        }
                    }
                }
            } else if ($config->server->domain
                && $host != $config->server->domain
                && (!$config->server->noRedirectPattern || !preg_match('/'.$config->server->noRedirectPattern.'/', $host))
            ) {
                $redirect = $config->server->domain;
            }
            if ($redirect) {
                header("Location: http://".$redirect.$_SERVER['REQUEST_URI'], true, 301);
                exit;
            }
        }

        if ($config->showPlaceholder
                && !$config->ignoreShowPlaceholder
                && php_sapi_name() != 'cli'
                && isset($_SERVER['REQUEST_URI'])
                && substr($_SERVER['REQUEST_URI'], 0, 8)!='/assets/'
        ) {
            $view = new Vps_View();
            echo $view->render('placeholder.tpl');
            exit;
        }

        if (php_sapi_name() != 'cli' && $config->preLogin && !isset($_COOKIE['unitTest'])
            && isset($_SERVER['REDIRECT_URL']) && substr($_SERVER['REDIRECT_URL'], 0, 10) != '/vps/test/'
             && substr($_SERVER['REDIRECT_URL'], 0, 7) != '/output' /*hack für rssinclude-test*/
             && substr($_SERVER['REDIRECT_URL'], 0, 11) != '/paypal_ipn'
        ) {
            $sessionPhpAuthed = new Zend_Session_Namespace('PhpAuth');
            if (empty($sessionPhpAuthed->success)) {
                if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
                    $loginResponse = Zend_Registry::get('userModel')
                        ->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                    if ($loginResponse['zendAuthResultCode'] == Zend_Auth_Result::SUCCESS) {
                        $sessionPhpAuthed->success = 1;
                    } else {
                        unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                    }
                }

                // separate if abfrage, damit login wieder kommt, falls gerade falsch eingeloggt wurde
                if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
                    header('WWW-Authenticate: Basic realm="Testserver"');
                    throw new Vps_Exception_AccessDenied();
                }
            }
        }

        if ($tl = $config->debug->timeLimit) {
            set_time_limit($tl);
        }
    }

    public static function shutDown()
    {
        if (Zend_Registry::get('config')->debug->querylog && php_sapi_name() != 'cli') {
            header('X-Vps-DbQueries: '.Vps_Db_Profiler::getCount());
        }
        Vps_Benchmark::shutDown();
    }

    public static function createDb()
    {
        $dao = Zend_Registry::get('dao');
        if (!$dao) return false;
        return $dao->getDb();
    }

    public static function createDao()
    {
        return new Vps_Dao();
    }

    public static function getConfigSection()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        //www abschneiden damit www.test und www.preview usw auch funktionieren
        if (substr($host, 0, 4)== 'www.') $host = substr($host, 4);

        if (php_sapi_name() == 'cli') {
            //wenn über kommandozeile aufgerufen
            $path = getcwd();
        } else {
            $path = $_SERVER['SCRIPT_FILENAME'];
        }
        if (file_exists('/var/www/vivid-test-server')) {
            return 'vivid-test-server';
        } else if (preg_match('#/www/(usr|public)/([0-9a-z-]+)/#', $path, $m)) {
            if ($m[2]=='vps-projekte') return 'vivid';
            return $m[2];
        } else if (substr($host, 0, 9)=='dev.test.') {
            return 'devtest';
        } else if (substr($host, 0, 4)=='dev.') {
            return 'dev';
        } else if (substr($host, 0, 5)=='test.' ||
                   substr($path, 0, 17) == '/docs/vpcms/test.' ||
                   substr($path, 0, 21) == '/docs/vpcms/www.test.' ||
                   substr($path, 0, 25) == '/var/www/html/vpcms/test.' ||
                   substr($path, 0, 20) == '/var/www/vpcms/test.') {
            return 'test';
        } else if (substr($host, 0, 5)=='demo.' ||
                   substr($path, 0, 17) == '/docs/vpcms/demo.' ||
                   substr($path, 0, 21) == '/docs/vpcms/www.demo.' ||
                   substr($path, 0, 25) == '/var/www/html/vpcms/demo.' ||
                   substr($path, 0, 20) == '/var/www/vpcms/demo.') {
            return 'demo';
        } else if (substr($host, 0, 8)=='preview.') {
            return 'preview';
        } else {
            return 'production';
        }
    }

    public static function dispatchVpc()
    {
        if (!isset($_SERVER['REDIRECT_URL'])) return;

        $uri = substr($_SERVER['REDIRECT_URL'], 1);
        $i = strpos($uri, '/');
        if ($i) $uri = substr($uri, 0, $i);
        if (!in_array($uri, array('media', 'vps', 'admin', 'assets'))) {
            $requestUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'];

            Vps_Registry::get('trl')->setUseUserLanguage(false);

            $root = Vps_Component_Data_Root::getInstance();
            $data = $root->getPageByUrl($requestUrl);
            if (!$data) {
               throw new Vps_Exception_NotFound();
            }
            $root->setCurrentPage($data);
            if ($data->url != $_SERVER['REDIRECT_URL']) {
                header('Location: '.$data->url);
                exit;
            }
            $page = $data->getComponent();
            $page->sendContent();

            Vps_Benchmark::shutDown();

            if ($page instanceof Vpc_Abstract_Feed_Component) {
                echo "<!--";
            }
            Vps_Benchmark::output();
            if ($page instanceof Vpc_Abstract_Feed_Component) {
                echo "-->";
            }
            exit;
        }
    }

    public static function dispatchMedia()
    {
        if (!isset($_SERVER['REDIRECT_URL'])) return;

        $urlParts = explode('/', substr($_SERVER['REDIRECT_URL'], 1));
        if (is_array($urlParts) && count($urlParts) == 2 && $urlParts[0] == 'media'
            && $urlParts[1] == 'headline'
        ) {
            Vps_Media_Headline::outputHeadline($_GET['selector'], $_GET['text']);
        } else if (is_array($urlParts) && $urlParts[0] == 'media') {
            if (sizeof($urlParts) != 6) {
                throw new Vps_Exception_NotFound();
            }
            $class = $urlParts[1];
            $id = $urlParts[2];
            $type = $urlParts[3];
            $checksum = $urlParts[4];
            $filename = $urlParts[5];

            if ($checksum != Vps_Media::getChecksum($class, $id, $type, $filename)) {
                throw new Vps_Exception_AccessDenied('Access to file not allowed.');
            }
            Vps_Media_Output::output(Vps_Media::getOutput($class, $id, $type));
        }
    }
}
