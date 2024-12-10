<?php
//instanceof operator doesn't work for strings correctly, found on php.net
function is_instance_of($sub, $super)
{
    $sub = is_object($sub) ? get_class($sub) : (string)$sub;
    $sub = strpos($sub, '.') ? substr($sub, 0, strpos($sub, '.')) : $sub;
    $super = is_object($super) ? get_class($super) : (string)$super;

    $ret = false;
    if ($sub === $super) {
        $ret = true;
    //} else if (is_subclass_of($sub, $super)) { disabled because of https://bugs.php.net/bug.php?id=50360, fixed for php 5.3
    } else if (in_array($super, class_parents($sub))) {
        $ret = true;
    } else if (in_array($super, class_implements($sub))) {
        $ret = true;
    }
    return $ret;
}

class Kwf_Setup
{
    public static $configClass;
    public static $configSection;
    const CACHE_SETUP_VERSION = 5; //increase version if incompatible changes to generated file are made

    public static function setUp($configClass = 'Kwf_Config_Web')
    {
        error_reporting(E_ALL & ~E_STRICT);
        define('APP_PATH', getcwd());
        Kwf_Setup::$configClass = $configClass;
        if (php_sapi_name() == 'cli') {
            //don't use cached setup on cli so clear-cache will always work even if eg. paths change
            require_once dirname(__FILE__).'/../Kwf/Util/Setup.php';
            Kwf_Util_Setup::minimalBootstrap();
            $setupCode = Kwf_Util_Setup::generateCode();
            Zend_Registry::_unsetInstance();
            eval(substr($setupCode, 5));
        } else if (!@include(APP_PATH.'/cache/setup'.self::CACHE_SETUP_VERSION.'.php')) {
            if (!file_exists(APP_PATH.'/cache/setup'.self::CACHE_SETUP_VERSION.'.php')) {
                require_once dirname(__FILE__).'/../Kwf/Util/Setup.php';
                Kwf_Util_Setup::minimalBootstrapAndGenerateFile();
            }
            include(APP_PATH.'/cache/setup'.self::CACHE_SETUP_VERSION.'.php');
        }
        if (!defined('VKWF_PATH') && php_sapi_name() != 'cli' && self::getBaseUrl() === null) {
            //if server.baseUrl is not set try to auto detect it and generate config.local.ini accordingly
            //this code is not used if server.baseUrl is set to "" in vkwf
            if (!isset($_SERVER['PHP_SELF'])) {
                echo "Can't detect baseUrl, PHP_SELF is not set\n";
                exit(1);
            }
            $baseUrl = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
            if (substr($baseUrl, -16) == '/kwf/maintenance') {
                $baseUrl = substr($baseUrl, 0, -16);
            }
            $cfg  = "[production]\n";
            $cfg .= "server.domain = \"$_SERVER[HTTP_HOST]\"\n";
            $cfg .= "server.baseUrl = \"$baseUrl\"\n";
            $cfg .= "setupFinished = false\n";

            if (file_exists('config.local.ini') && filesize('config.local.ini')>0) {
                echo "config.local.ini already exists but server.baseUrl is not set\n";
                exit(1);
            }
            if (!is_writable('.')) {
                echo "'".getcwd()."' is not writable, can't create config.local.ini\n";
                exit(1);
            }
            file_put_contents('config.local.ini', $cfg);
            Kwf_Config_Web::reload();
            Kwf_Config::deleteValueCache('server.domain');
            Kwf_Config::deleteValueCache('server.baseUrl');
            unlink('cache/setup'.self::CACHE_SETUP_VERSION.'.php');
            echo "<h1>".Kwf_Config::getValue('application.name')."</h1>\n";
            echo "<a href=\"$baseUrl/kwf/maintenance/setup\">[start setup]</a>\n";
            exit;
        }

        if (isset($_SERVER['REQUEST_URI']) && substr($_SERVER['REQUEST_URI'], 0, 5) == '/kwf/') {
            if (substr($_SERVER['REQUEST_URI'], 0, 9) == '/kwf/pma/' || $_SERVER['REQUEST_URI'] == '/kwf/pma') {
                Kwf_Util_Pma::dispatch();
            } else if ($_SERVER['REQUEST_URI'] == '/kwf/check') {
                $ok = true;
                $msg = '';
                if (Kwf_Setup::hasDb()) {
                    $date = Kwf_Registry::get('db')->query("SELECT NOW()")->fetchColumn();
                    if (!$date) {
                        $ok = false;
                        $msg .= 'mysql connection failed';
                    }
                }
                if (file_exists('instance_startup')) {
                    //can be used while starting up autoscaling instances
                    $ok = false;
                    $msg .= 'instance startup in progress';
                }
                if (!$ok) {
                    header("HTTP/1.0 500 Error");
                    echo "<h1>Check failed</h1>";
                    echo $msg;
                } else {
                    echo "ok";
                }
                exit;
            }
        }

        Kwf_Benchmark::checkpoint('setUp');
    }

    public static function shutDown()
    {
        chdir(APP_PATH);
        $error = error_get_last();
        if ($error !== null) {
            ini_set('memory_limit', memory_get_usage()+16*1024*1024); //in case it was an memory limit error make sure we have enough memory for error handling
            $ignore = false;
            if (preg_match('#^include\(\).*Failed opening \'[^\']*cache/setup\d+.php\' for inclusion#', $error['message'])) {
                //ignore error that can happen before creating setup the first time
                $ignore = true;
            }
            if (defined('E_STRICT') && $error["type"] == E_STRICT) {
                $ignore = true;
            }
            if (defined('E_DEPRECATED') && $error["type"] == E_DEPRECATED) {
                $ignore = true;
            }
            if (!$ignore) {
                $e = new ErrorException($error["message"], 0, $error["type"], $error["file"], $error["line"]);
                Kwf_Debug::handleException($e);
            }
        }
        Kwf_Benchmark::shutDown();
    }

    public static function createDb()
    {
        $dao = Zend_Registry::get('dao');
        if (!$dao) return false;
        return $dao->getDb();
    }

    public static function hasDb()
    {
        static $ret;
        if (isset($ret)) return $ret;

        $config = Kwf_Config::getValueArray('database');
        $ret = isset($config['web']) && $config['web']!==false;
        return $ret;
    }

    public static function createDao()
    {
        if (!self::hasDb()) {
            return null;
        }
        return new Kwf_Dao();
    }

    public static function getConfigSection()
    {
        if (is_null(self::$configSection)) {
            throw new Kwf_Exception("Config Section not yet set");
        }
        return self::$configSection;
    }

    public static function getRequestPath()
    {
        static $requestPath;
        if (isset($requestPath)) return $requestPath;
        switch (php_sapi_name()) {
            case 'apache2handler':
            case 'apache':
            case 'fpm-fcgi':
                $requestPath = $_SERVER['REQUEST_URI'];
                $requestPath = strtok($requestPath, '?');
                break;
            case 'cli':
                $requestPath = false;
                break;
            case 'cli-server':
                $requestPath = $_SERVER['SCRIPT_NAME'];
                break;
            case 'cgi-fcgi':
                $requestPath = $_SERVER['SCRIPT_URL'];
                break;
            default:
                throw new Kwf_Exception("unsupported sapi: ".php_sapi_name());
        }
        return $requestPath;
    }

    public static function getBaseUrl()
    {
        static $ret;
        if (isset($ret)) return $ret;
        $ret = Kwf_Config::getValue('server.baseUrl');
        return $ret;
    }

    public static function dispatchKwc()
    {
        $requestPath = self::getRequestPath();
        if ($requestPath === false) return;
        $fullRequestPath = $requestPath;

        $data = null;
        $baseUrl = Kwf_Setup::getBaseUrl();
        if ($baseUrl) {
            if (substr($requestPath, 0, strlen($baseUrl)) != $baseUrl) {
                throw new Kwf_Exception_NotFound();
            }
            $requestPath = substr($requestPath, strlen($baseUrl));
        }
        $uri = substr($requestPath, 1);
        $i = strpos($uri, '/');
        if ($i) $uri = substr($uri, 0, $i);

        if ($uri == 'robots.txt') {
            Kwf_Util_RobotsTxt::output();
        }

        if ($uri == 'sitemap.xml') {
            $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$_SERVER['HTTP_HOST'].Kwf_Setup::getBaseUrl().'/', null);
            Kwf_Component_Sitemap::output($data->getDomainComponent());
        }
        if (!in_array($uri, array('media', 'kwf', 'admin', 'assets', 'vkwf', 'api'))) {
            if (!isset($_SERVER['HTTP_HOST'])) {
                $requestUrl = 'http://'.Kwf_Config::getValue('server.domain').$fullRequestPath;
            } else {
                $requestUrl = 'http://'.$_SERVER['HTTP_HOST'].$fullRequestPath;
            }

            Kwf_Trl::getInstance()->setUseUserLanguage(false);

            $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
            $root = Kwf_Component_Data_Root::getInstance();
            $exactMatch = true;
            $data = $root->getPageByUrl($requestUrl, $acceptLanguage, $exactMatch);
            Kwf_Benchmark::checkpoint('getPageByUrl');
            if (!$data) {
                throw new Kwf_Exception_NotFound();
            }
            if (!$exactMatch) {
                if (rawurldecode($data->url) == $fullRequestPath) {
                    throw new Kwf_Exception("getPageByUrl reported this isn't an exact match, but the urls are equal. wtf.");
                }
                $url = $data->url;
                if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) $url .= '?' . $_SERVER['QUERY_STRING'];
                if (!$url) { // e.g. firstChildPageData without child pages
                    throw new Kwf_Exception_NotFound();
                }
                header('Location: '.$url, true, 301);
                exit;
            }
            $root->setCurrentPage($data);

            Kwf_User_Autologin::processCookies();

            $contentSender = Kwf_Component_Settings::getSetting($data->componentClass, 'contentSender');
            $contentSender = new $contentSender($data);
            $contentSender->sendContent(true);
            Kwf_Benchmark::shutDown();

            //TODO: ein flag oder sowas ähnliches stattdessen verwenden
            if ($data instanceof Kwc_Abstract_Feed_Component || $data instanceof Kwc_Export_Xml_Component || $data instanceof Kwc_Export_Xml_Trl_Component) {
                echo "<!--";
            }
            Kwf_Benchmark::output();
            if ($data instanceof Kwc_Abstract_Feed_Component || $data instanceof Kwc_Export_Xml_Component || $data instanceof Kwc_Export_Xml_Trl_Component) {
                echo "-->";
            }
            exit;

        } else if ($requestPath == '/kwf/util/kwc/render') {
            Kwf_User_Autologin::processCookies();
            Kwf_Util_Component::dispatchRender();
        }
    }

    public static function dispatchMedia()
    {
        $requestPath = self::getRequestPath();
        if ($requestPath === false) return;

        $baseUrl = Kwf_Setup::getBaseUrl();
        if ($baseUrl) {
            if (substr($requestPath, 0, strlen($baseUrl)) != $baseUrl) {
                throw new Kwf_Exception_NotFound();
            }
            $requestPath = substr($requestPath, strlen($baseUrl));
        }
        $urlParts = explode('/', substr($requestPath, 1));
        if (is_array($urlParts) && $urlParts[0] == 'media') {
            if (sizeof($urlParts) != 7) {
                throw new Kwf_Exception_NotFound();
            }
            $class = $urlParts[1];
            $id = $urlParts[2];
            $type = $urlParts[3];
            $checksum = urlencode($urlParts[4]);
            // time() wäre der 5er, wird aber nur wegen browsercache benötigt
            $filename = $urlParts[6];

            if ($checksum != Kwf_Media::getChecksum($class, $id, $type, $filename)) {
                throw new Kwf_Exception_NotFound();
            }
            $class = rawurldecode($class);
            Kwf_Media_Output::output(Kwf_Media::getOutput($class, $id, $type));
        }
    }

    public static function getHost($includeProtocol = true)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Config::getValue('server.domain');
        }
        if ($includeProtocol) $host = 'http://' . $host;
        return $host;
    }

    /**
     * Check if user is logged in (faster than directly calling user model)
     *
     * Only asks user model (expensive) when there is something stored in the session
     *
     * @return boolean if user is logged in
     */
    public static function hasAuthedUser()
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();
        if ($benchmarkEnabled) $t = microtime(true);
        if (!Zend_Session::isStarted() &&
            !Zend_Session::sessionExists() &&
            !Kwf_Config::getValue('autologin')
        ) {
            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('hasAuthedUser: no session', microtime(true)-$t);
            return false;
        }
        if (!Kwf_Auth::getInstance()->getStorage()->read()) {
            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('hasAuthedUser: storage empty', microtime(true)-$t);
            return false;
        }
        $m = Kwf_Registry::get('userModel');
        if (!$m) return false;
        $ret = $m->hasAuthedUser();
        if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('hasAuthedUser: asked model', microtime(true)-$t);
        return $ret;
    }
}
