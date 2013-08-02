<?php

//instanceof operator geht für strings ned korrekt, von php.net gfladad
function is_instance_of($sub, $super)
{
    $sub = is_object($sub) ? get_class($sub) : (string)$sub;
    $sub = strpos($sub, '.') ? substr($sub, 0, strpos($sub, '.')) : $sub;
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

class Kwf_Setup
{
    public static $configClass;
    public static $configSection;
    const CACHE_SETUP_VERSION = 2; //increase version if incompatible changes to generated file are made

    public static function setUp($configClass = 'Kwf_Config_Web')
    {
        error_reporting(E_ALL & ~E_STRICT);
        define('APP_PATH', getcwd());
        Kwf_Setup::$configClass = $configClass;
        if (!@include('./cache/setup'.self::CACHE_SETUP_VERSION.'.php')) {
            if (!file_exists('cache/setup'.self::CACHE_SETUP_VERSION.'.php')) {
                require_once dirname(__FILE__).'/../Kwf/Util/Setup.php';
                Kwf_Util_Setup::minimalBootstrapAndGenerateFile();
            }
            include('cache/setup'.self::CACHE_SETUP_VERSION.'.php');
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
        return self::$configSection;
    }

    public static function getRequestPath()
    {
        static $requestPath;
        if (isset($requestPath)) return $requestPath;
        switch (php_sapi_name()) {
            case 'apache2handler':
                $requestPath = $_SERVER['REDIRECT_URL'];
                break;
            case 'cli':
                $requestPath = false;
                break;
            case 'cli-server':
                $requestPath = $_SERVER['SCRIPT_NAME'];
                break;
            default:
                throw new Kwf_Exception("unsupported sapi: ".php_sapi_name());
        }
        return $requestPath;
    }

    public static function dispatchKwc()
    {
        $requestPath = self::getRequestPath();
        if ($requestPath === false) return;

        $data = null;
        $uri = substr($requestPath, 1);
        $i = strpos($uri, '/');
        if ($i) $uri = substr($uri, 0, $i);
        $urlPrefix = Kwf_Config::getValue('kwc.UrlPrefix');

        if ($uri == 'robots.txt') {
            Kwf_Media_Output::output(array(
                'contents' => "User-agent: *\nDisallow: /admin/",
                'mimeType' => 'text/plain'
            ));
        }

        if ($uri == 'sitemap.xml') {
            $sitemap = new Kwf_Component_Sitemap();
            $sitemap->outputSitemap(Kwf_Component_Data_Root::getInstance());
        }

        if (!in_array($uri, array('media', 'kwf', 'admin', 'assets', 'vkwf'))
            && (!$urlPrefix || substr($requestPath, 0, strlen($urlPrefix)) == $urlPrefix)
        ) {
            if (!isset($_SERVER['HTTP_HOST'])) {
                $requestUrl = 'http://'.Kwf_Config::getValue('server.domain').$requestPath;
            } else {
                $requestUrl = 'http://'.$_SERVER['HTTP_HOST'].$requestPath;
            }

            Kwf_Trl::getInstance()->setUseUserLanguage(false);
            self::_setLocale();

            $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
            $root = Kwf_Component_Data_Root::getInstance();
            $exactMatch = true;
            $data = $root->getPageByUrl($requestUrl, $acceptLanguage, $exactMatch);
            Kwf_Benchmark::checkpoint('getPageByUrl');
            if (!$data) {
                throw new Kwf_Exception_NotFound();
            }
            if (!$exactMatch) {
                if (rawurldecode($data->url) == $requestPath) {
                    throw new Kwf_Exception("getPageByUrl reported this isn't an exact match, but the urls are equal. wtf.");
                }
                $url = $data->url;
                if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) $url .= '?' . $_SERVER['QUERY_STRING'];
                if (!$url) { // e.g. firstChildPageData without child pages
                    throw new Kwf_Exception_NotFound();
                }
                header('Location: ' . $url);
                exit;
            }
            $root->setCurrentPage($data);
            $contentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
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
            Kwf_Util_Component::dispatchRender();
        }
    }

    public static function dispatchMedia()
    {
        $requestPath = self::getRequestPath();
        if ($requestPath === false) return;

        $urlParts = explode('/', substr($requestPath, 1));
        if (is_array($urlParts) && count($urlParts) == 2 && $urlParts[0] == 'media'
            && $urlParts[1] == 'headline'
        ) {
            Kwf_Media_Headline::outputHeadline($_GET['selector'], $_GET['text'], $_GET['assetsType']);
        } else if (is_array($urlParts) && $urlParts[0] == 'media') {
            if (sizeof($urlParts) != 7) {
                throw new Kwf_Exception_NotFound();
            }
            $class = $urlParts[1];
            $id = $urlParts[2];
            $type = $urlParts[3];
            $checksum = $urlParts[4];
            // time() wäre der 5er, wird aber nur wegen browsercache benötigt
            $filename = $urlParts[6];

            if ($checksum != Kwf_Media::getChecksum($class, $id, $type, $filename)) {
                throw new Kwf_Exception_AccessDenied('Access to file not allowed.');
            }
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

    private static function _setLocale()
    {
        /*
            Das LC_NUMERIC wird absichtlich ausgenommen weil:
            Wenn locale auf DE gesetzt ist und man aus der DB Kommazahlen
            ausliest, dann kommen die als string mit Beistrich (,) an und mit
            dem lässt sich nicht weiter rechnen.
            PDO oder Zend machen da wohl den Fehler und ändern irgendwo die
            PHP-Float repräsentation in einen String um und so steht er dann mit
            Beistrich drin.
            Beispiel:
                setlocale(LC_ALL, 'de_DE');
                $a = 2.3;
                echo $a; // gibt 2,3 aus
                echo $a * 2; // gibt 4,6 aus
            Problem ist es dann, wenn die kommazahl in string gecastet wird:
                setlocale(LC_ALL, 'de_DE');
                $a = 2.3;
                $b = "$a";
                echo $b; // gibt 2,3 aus
                echo $b * 2; // gibt 4 aus -> der teil hinterm , wird einfach ignoriert
        */
        setlocale(LC_ALL, explode(', ', trlcKwf('locale', 'C')));
        setlocale(LC_NUMERIC, 'C');
    }
}
