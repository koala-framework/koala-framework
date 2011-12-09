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

    public static function setUp($configClass = 'Kwf_Config_Web')
    {
        error_reporting(E_ALL);
        if (!@include('./cache/setup.php')) {
            if (!file_exists('cache/setup.php')) {
                define('KWF_PATH', realpath(dirname(__FILE__).'/..'));
                if (file_exists(KWF_PATH.'/include_path')) {
                    $zendPath = trim(file_get_contents(KWF_PATH.'/include_path'));
                    $zendPath = str_replace(
                        '%version%',
                        file_get_contents(KWF_PATH.'/include_path_version'),
                        $zendPath);

                } else {
                    die ('zend not found');
                }
                set_include_path(get_include_path(). PATH_SEPARATOR . KWF_PATH . PATH_SEPARATOR . $zendPath);

                require_once 'Kwf/Loader.php';
                Kwf_Loader::registerAutoload();

                Kwf_Setup::$configClass = $configClass;
                require_once 'Kwf/Registry.php';
                Zend_Registry::setClassName('Kwf_Registry');

                require_once 'Kwf/Trl.php';

                umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen

                require_once 'Kwf/Util/Setup.php';
                file_put_contents('cache/setup.php', Kwf_Util_Setup::generateCode($configClass));

                Zend_Registry::_unsetInstance(); //cache/setup.php will call setClassName again
            }
            include('cache/setup.php');
        }
    }

    public static function shutDown()
    {
        if (Kwf_Config::getValue('debug.querylog') && php_sapi_name() != 'cli') {
            header('X-Kwf-DbQueries: '.Kwf_Db_Profiler::getCount());
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
        $ret = isset($config['web']);
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

    public static function dispatchKwc()
    {
        if (!isset($_SERVER['REDIRECT_URL'])) return;

        $data = null;
        $uri = substr($_SERVER['REDIRECT_URL'], 1);
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

        if (!in_array($uri, array('media', 'kwf', 'admin', 'assets'))
            && (!$urlPrefix || substr($_SERVER['REDIRECT_URL'], 0, strlen($urlPrefix)) == $urlPrefix)
        ) {
            if (!isset($_SERVER['HTTP_HOST'])) {
                throw new Kwf_Exception_NotFound();
            }

            $requestUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL'];

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
                if (rawurldecode($data->url) == $_SERVER['REDIRECT_URL']) {
                    throw new Kwf_Exception("getPageByUrl reported this isn't an exact match, but the urls are equal. wtf.");
                }
                $url = $data->url;
                if (isset($_SERVER['QUERY_STRING'])) $url .= '?' . $_SERVER['QUERY_STRING'];
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

        } else if ($_SERVER['REDIRECT_URL'] == '/kwf/util/kwc/render') {
            Kwf_Util_Component::dispatchRender();
        }
    }

    public static function dispatchMedia()
    {
        if (!isset($_SERVER['REDIRECT_URL'])) return;

        $urlParts = explode('/', substr($_SERVER['REDIRECT_URL'], 1));
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
