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
        if (PHP_SAPI == 'cli') {
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
            if (defined('E_USER_DEPRECATED') && $error["type"] == E_USER_DEPRECATED) {
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
        switch (PHP_SAPI) {
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
                throw new Kwf_Exception("unsupported sapi: ".PHP_SAPI);
        }
        return $requestPath;
    }

    public static function dispatchKwc()
    {
        $requestPath = self::getRequestPath();
        if ($requestPath === false) return;
        $fullRequestPath = $requestPath;

        $data = null;
        $uri = substr($requestPath, 1);
        $i = strpos($uri, '/');
        if ($i) $uri = substr($uri, 0, $i);

        if ($uri == 'robots.txt' || $uri == 'sitemap.xml') {
            self::restrictRequestMethod();
            $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$_SERVER['HTTP_HOST'].'/', null);

            if ($uri == 'robots.txt') {
                $robotsTxtClass = Kwf_Config::getValue('robotsTxtClass');
                $robotsTxtClass::output($data);
            }

            if ($uri == 'sitemap.xml') {
                $sitemapClass = Kwf_Config::getValue('sitemapClass');
                $sitemapClass::output($data->getDomainComponent());
            }
        }

        if (!in_array($uri, array('media', 'kwf', 'admin', 'assets', 'vkwf', 'api'))) {
            self::restrictRequestMethod();

            if (!isset($_SERVER['HTTP_HOST'])) {
                $requestUrl = 'http://'.Kwf_Config::getValue('server.domain').$fullRequestPath;
            } else {
                $requestUrl = 'http://'.$_SERVER['HTTP_HOST'].$fullRequestPath;
            }

            Kwf_Trl::getInstance()->setUseUserLanguage(false);

            $root = Kwf_Component_Data_Root::getInstance();

            foreach ($root->getPlugins('Kwf_Component_PluginRoot_Interface_PreDispatch') as $p) {
                $p->preDispatch($requestUrl);
            }

            $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
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
                if (!$url) { // e.g. firstChildPageData without child pages
                    throw new Kwf_Exception_NotFound();
                }
                foreach ($root->getPlugins('Kwf_Component_PluginRoot_Interface_PostRender') as $p) {
                    $url = $p->processUrl($url);
                }
                if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) $url .= '?' . $_SERVER['QUERY_STRING'];
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
            self::restrictRequestMethod();

            Kwf_User_Autologin::processCookies();
            Kwf_Util_Component::dispatchRender();
        }
    }

    public static function dispatchMedia()
    {
        $requestPath = self::getRequestPath();
        if ($requestPath === false) return;

        $urlParts = explode('/', substr($requestPath, 1));
        if (is_array($urlParts) && $urlParts[0] == 'media') {
            self::restrictRequestMethod();

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
            $output = Kwf_Media::getOutput($class, $id, $type);
            Kwf_Media_Output::outputWithoutShutdown($output);
            if (isset($output['file'])) {
                Kwf_Util_Media::onCacheFileRead($output['file']);
            }
            Kwf_Benchmark::shutDown();
            exit;
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
     * Only asks user model (expensive) when there is something stored in the session or class already loaded
     *
     * @return boolean if user is logged in
     */
    public static function hasAuthedUser()
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();
        if ($benchmarkEnabled) $t = microtime(true);
        if (class_exists('Kwf_User_Model', false)) {
            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('hasAuthedUser: Kwf_User_Model already loaded, asked model', microtime(true)-$t);
            $m = Kwf_Registry::get('userModel');
            if (!$m) return false;
            $ret = $m->hasAuthedUser();
        } else {
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
        }
        return $ret;
    }

    /**
     * Is used in generated setup.php (cache/setupX.php) and in ContentSender_Default.php
     * @throws Kwf_Exception_Unauthorized
     */
    public static function checkPreLogin($requiredUsername, $requiredPassword)
    {
        $authUser = !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : false;
        $authPW = !empty($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : false;

        $kwfAuthorization = null;
        if (isset($_SERVER['HTTP_X_KWF_AUTHORIZATION'])) {
            $kwfAuthorization = $_SERVER['HTTP_X_KWF_AUTHORIZATION'];
        } else if (isset($_COOKIE['KWF-Authorization'])) {
            $kwfAuthorization = $_COOKIE['KWF-Authorization'];
        }

        if ($kwfAuthorization !== null) {
            $authValue = explode(' ', $kwfAuthorization);
            if (count($authValue) === 2 && strtolower($authValue[0]) === 'basic') {
                $authorization = explode(':', base64_decode($authValue[1]));
                if (count($authorization) === 2) {
                    $authUser = $authorization[0];
                    $authPW = $authorization[1];
                }
            }
        }

        if (!$authUser || !$authPW
            || $authUser !== $requiredUsername
            || $authPW !== $requiredPassword
        ) {
            header('WWW-Authenticate: Basic realm="Page locked by preLogin"');
            $exception = new Kwf_Exception_Unauthorized('PreLogin required');
            $exception->setPasswordToMask($requiredPassword);
            throw $exception;
        }
    }

    /**
     * @throws Kwf_Exception_MethodNotAllowed
     */
    public static function restrictRequestMethod()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
        if ($method && !in_array($method, array('HEAD', 'GET', 'POST'))) throw new Kwf_Exception_MethodNotAllowed($method);
    }
}
