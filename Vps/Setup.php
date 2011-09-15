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

class Vps_Setup
{
    public static $configClass;
    public static $configSection;

    public static function setUp($configClass = 'Vps_Config_Web')
    {
        error_reporting(E_ALL);
        if (!include('application/cache/setup.php')) {
            if (!file_exists('application/cache/setup.php')) {
                if (file_exists(VPS_PATH.'/include_path')) {
                    $zendPath = trim(file_get_contents(VPS_PATH.'/include_path'));
                    $zendPath = str_replace(
                        '%version%',
                        file_get_contents(VPS_PATH.'/include_path_version'),
                        $zendPath);
                } else {
                    die ('zend not found');
                }
                set_include_path(get_include_path(). PATH_SEPARATOR . $zendPath);

                require_once 'Vps/Loader.php';
                Vps_Loader::registerAutoload();

                Vps_Setup::$configClass = $configClass;
                require_once 'Vps/Registry.php';
                Zend_Registry::setClassName('Vps_Registry');

                require_once 'Vps/Trl.php';

                umask(000); //nicht 002 weil wwwrun und vpcms in unterschiedlichen gruppen

                $path = getcwd();
                if (file_exists('application/config_section')) {
                    Vps_Setup::$configSection = trim(file_get_contents('application/config_section'));
                } else if (file_exists('/var/www/vivid-test-server')) {
                    Vps_Setup::$configSection = 'vivid-test-server';
                } else if (preg_match('#/(www|wwwnas)/(usr|public)/([0-9a-z-]+)/#', $path, $m)) {
                    if ($m[3]=='vps-projekte') return 'vivid';
                    Vps_Setup::$configSection = $m[3];
                } else if (substr($path, 0, 17) == '/docs/vpcms/test.' ||
                        substr($path, 0, 21) == '/docs/vpcms/www.test.' ||
                        substr($path, 0, 25) == '/var/www/html/vpcms/test.' ||
                        substr($path, 0, 20) == '/var/www/vpcms/test.') {
                    Vps_Setup::$configSection = 'test';
                } else {
                    Vps_Setup::$configSection = 'production';
                }

                require_once 'Vps/Util/Setup.php';
                file_put_contents('application/cache/setup.php', Vps_Util_Setup::generateCode($configClass));
                die('created application/cache/setup.php');
            }
            include('application/cache/setup.php');
        }
    }

    public static function shutDown()
    {
        if (Vps_Config::getValue('debug.querylog') && php_sapi_name() != 'cli') {
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

    public static function hasDb()
    {
        $cacheId = 'hasDb';
        $ret = Vps_Cache_Simple::fetch($cacheId, $success);
        if (!$success) {
            $ret = file_exists('application/config.db.ini');
            Vps_Cache_Simple::add($cacheId, $ret);
        }
        return $ret;
    }

    public static function createDao()
    {
        if (!self::hasDb()) {
            return null;
        }
        return new Vps_Dao();
    }

    public static function getConfigSection()
    {
        return self::$configSection;
    }

    public static function dispatchVpc()
    {
        if (!isset($_SERVER['REDIRECT_URL'])) return;

        $data = null;
        $uri = substr($_SERVER['REDIRECT_URL'], 1);
        $i = strpos($uri, '/');
        if ($i) $uri = substr($uri, 0, $i);
        $urlPrefix = Vps_Config::getValue('vpc.UrlPrefix');

        if ($uri == 'robots.txt') {
            Vps_Media_Output::output(array(
                'contents' => "User-agent: *\nDisallow: /admin/",
                'mimeType' => 'text/plain'
            ));
        }

        if ($uri == 'sitemap.xml') {
            $sitemap = new Vps_Component_Sitemap();
            $sitemap->outputSitemap(Vps_Component_Data_Root::getInstance());
        }

        if (!in_array($uri, array('media', 'vps', 'admin', 'assets'))
            && (!$urlPrefix || substr($_SERVER['REDIRECT_URL'], 0, strlen($urlPrefix)) == $urlPrefix)
        ) {
            if (!isset($_SERVER['HTTP_HOST'])) {
                throw new Vps_Exception_NotFound();
            }

            $requestUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'];

            Vps_Trl::getInstance()->setUseUserLanguage(false);
            self::_setLocale();

            $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
            $root = Vps_Component_Data_Root::getInstance();
            $exactMatch = true;
            $data = $root->getPageByUrl($requestUrl, $acceptLanguage, $exactMatch);
            Vps_Benchmark::checkpoint('getPageByUrl');
            if (!$data) {
                throw new Vps_Exception_NotFound();
            }
            if (!$exactMatch) {
                if (rawurldecode($data->url) == $_SERVER['REDIRECT_URL']) {
                    throw new Vps_Exception("getPageByUrl reported this isn't an exact match, but the urls are equal. wtf.");
                }
                header('Location: '.$data->url);
                exit;
            }
            $root->setCurrentPage($data);
            $data->getComponent()->sendContent();
            Vps_Benchmark::shutDown();

            //TODO: ein flag oder sowas ähnliches stattdessen verwenden
            if ($data instanceof Vpc_Abstract_Feed_Component || $data instanceof Vpc_Export_Xml_Component || $data instanceof Vpc_Export_Xml_Trl_Component) {
                echo "<!--";
            }
            Vps_Benchmark::output();
            if ($data instanceof Vpc_Abstract_Feed_Component || $data instanceof Vpc_Export_Xml_Component || $data instanceof Vpc_Export_Xml_Trl_Component) {
                echo "-->";
            }
            exit;

        } else if ($_SERVER['REDIRECT_URL'] == '/vps/util/render/render') {

            if (!isset($_REQUEST['url']) || !$_REQUEST["url"]) {
                throw new Vps_Exception_Client('Need URL.');
            }
            $url = $_REQUEST['url'];
            $componentId = isset($_REQUEST['componentId']) ? $_REQUEST['componentId'] : null;
            $parsedUrl = parse_url($url);
            $_GET = array();
            if (isset($parsedUrl['query'])) {
                foreach (explode('&' , $parsedUrl['query']) as $get) {
                    if (!$get) continue;
                    $pos = strpos($get, '=');
                    $_GET[substr($get, 0, $pos)] = substr($get, $pos+1);
                }
            }
            if ($componentId) {
                $data = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
            } else {
                $data = Vps_Component_Data_Root::getInstance()->getPageByUrl($url, null);
            }
            if (!$data) throw new Vps_Exception_NotFound();
            $data->getComponent()->sendContent(false);
            Vps_Benchmark::shutDown();
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
            Vps_Media_Headline::outputHeadline($_GET['selector'], $_GET['text'], $_GET['assetsType']);
        } else if (is_array($urlParts) && $urlParts[0] == 'media') {
            if (sizeof($urlParts) != 7) {
                throw new Vps_Exception_NotFound();
            }
            $class = $urlParts[1];
            $id = $urlParts[2];
            $type = $urlParts[3];
            $checksum = $urlParts[4];
            // time() wäre der 5er, wird aber nur wegen browsercache benötigt
            $filename = $urlParts[6];

            if ($checksum != Vps_Media::getChecksum($class, $id, $type, $filename)) {
                throw new Vps_Exception_AccessDenied('Access to file not allowed.');
            }
            Vps_Media_Output::output(Vps_Media::getOutput($class, $id, $type));
        }
    }

    public static function getHost($includeProtocol = true)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Config::getValue('server.domain');
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
        setlocale(LC_ALL, explode(', ', trlcVps('locale', 'C')));
        setlocale(LC_NUMERIC, 'C');
    }
}
