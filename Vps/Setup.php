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

    public static function setUpZend()
    {
        if (file_exists(VPS_PATH.'/include_path')) {
            $zendPath = trim(file_get_contents(VPS_PATH.'/include_path'));
            $zendPath = str_replace(
                '%version%',
                file_get_contents(VPS_PATH.'/include_path_version'),
                $zendPath);
        } else {
            die ('zend not found');
        }
        $includePath  = get_include_path();
        $includePath .= PATH_SEPARATOR . $zendPath;
        set_include_path($includePath);

        require_once 'Vps/Loader.php';
    }

    public static function setUp($configClass = 'Vps_Config_Web')
    {
        require_once('Vps/Benchmark.php');
        Vps_Benchmark::$startTime = microtime(true);

        if (isset($_SERVER['HTTP_CLIENT_IP'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];

        self::setUpZend();

        //here to be as fast as possible (and have no session)
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

        //here to have less dependencies
        if (isset($_SERVER['REQUEST_URI']) &&
            substr($_SERVER['REQUEST_URI'], 0, 17) == '/vps/check-config'
        ) {
            Vps_Loader::registerAutoload();
            if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']!='vivid' || $_SERVER['PHP_AUTH_PW']!='planet') {
                header('WWW-Authenticate: Basic realm="Check Config"');
                throw new Vps_Exception_AccessDenied();
            }
            $quiet = isset($_GET['quiet']);
            Vps_Util_Check_Config::check($quiet);
        }
        if (php_sapi_name() == 'cli' && isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'check-config') {
            Vps_Loader::registerAutoload();
            $quiet = isset($_SERVER['argv'][2]) && $_SERVER['argv'][2] == 'quiet';
            Vps_Util_Check_Config::check($quiet);
        }

        self::setUpVps($configClass);
    }

    public static function setUpVps($configClass = 'Vps_Config_Web')
    {
        require_once 'Vps/Registry.php';

        Zend_Registry::setClassName('Vps_Registry');

        self::$configClass = $configClass;

        require_once 'Vps/Config.php';

        if (Vps_Config::getValue('debug.componentCache.checkComponentModification')) {
            $masterFiles = array(
                'application/config.ini',
                VPS_PATH . '/config.ini'
            );
            if (file_exists('application/vps_branch')) $masterFiles[] = 'application/vps_branch';
            require_once 'Vps/Config/Web.php';
            $mtime = Vps_Config_Web::getInstanceMtime(Vps_Setup::getConfigSection());
            foreach ($masterFiles as $f) {
                if (filemtime($f) > $mtime) {
                    Vps_Config::clearValueCache();
                    break;
                }
            }
        }

        if (Vps_Config::getValue('debug.benchmark')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            Vps_Benchmark::enable();
        }
        if (Vps_Config::getValue('debug.benchmarkLog')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            Vps_Benchmark::enableLog();
        }

        Vps_Loader::registerAutoload();

        require_once 'Vps/Debug.php';
        require_once 'Vps/Trl.php';


        ini_set('memory_limit', '128M');
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        mb_internal_encoding('UTF-8');
        iconv_set_encoding('internal_encoding', 'utf-8');
        set_error_handler(array('Vps_Debug', 'handleError'), E_ALL);
        set_exception_handler(array('Vps_Debug', 'handleException'));
        umask(000); //nicht 002 weil wwwrun und vpcms in unterschiedlichen gruppen

        $ip = get_include_path();
        foreach (Vps_Config::getValueArray('includepath') as $t=>$p) {
            if ($t == 'phpunit') {
                //vorne anh�ngen damit er vorrang vor /usr/share/php hat
                $ip = $p . PATH_SEPARATOR . $ip;
            } else {
                $ip .= PATH_SEPARATOR . $p;
            }
        }
        set_include_path($ip);

        Zend_Registry::set('requestNum', ''.floor(microtime(true)*100));

        if (Vps_Config::getValue('debug.firephp') && php_sapi_name() != 'cli') {
            require_once 'FirePHPCore/FirePHP.class.php';
            FirePHP::init();
        }

        if (Vps_Config::getValue('debug.querylog') && php_sapi_name() != 'cli') {
            header('X-Vps-RequestNum: '.Zend_Registry::get('requestNum'));
            register_shutdown_function(array('Vps_Setup', 'shutDown'));
        }
        if ((Vps_Config::getValue('debug.firephp') || Vps_Config::getValue('debug.querylog'))
                && php_sapi_name() != 'cli')
        {
            ob_start();
        }

        if (is_file('application/vps_branch') && trim(file_get_contents('application/vps_branch')) != Vps_Config::getValue('application.vps.version')) {
            $validCommands = array('shell', 'export', 'copy-to-test');
            if (php_sapi_name() != 'cli' || !isset($_SERVER['argv'][1]) || !in_array($_SERVER['argv'][1], $validCommands)) {
                $required = trim(file_get_contents('application/vps_branch'));
                $vpsBranch = Vps_Util_Git::vps()->getActiveBranch();
                throw new Vps_Exception_Client("Invalid Vps branch. Required: '$required', used: '".Vps_Config::getValue('application.vps.version')."' (Git branch '$vpsBranch')");
            }
        }

        if (isset($_POST['PHPSESSID'])) {
            //für swfupload
            Zend_Session::setId($_POST['PHPSESSID']);
        }

        if (isset($_COOKIE['unitTest'])) {
            //$config->debug->benchmark = false;
        }

        // Falls redirectToDomain eingeschalten ist, umleiten
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        if ($host && Vps_Config::getValue('server.redirectToDomain')) {
            $redirect = false;
            if ($domains = Vps_Config::getValueArray('vpc.domains')) {
                $noRedirect = false;
                foreach ($domains as $domain) {
                    if ($domain['domain'] == $host) {
                        $noRedirect = true;
                        break;
                    }
                }
                if (!$noRedirect) {
                    foreach ($domains as $domain) {
                        if (!$redirect && !isset($domain['pattern'])) $redirect = $domain['domain'];
                        if (isset($domain['pattern']) && preg_match('/' . $domain['pattern'] . '/', $host)
                        ) {
                            if ($domain['noRedirectPattern'] &&
                                preg_match('/'.$domain['noRedirectPattern'].'/', $host)
                            ) {
                                $redirect = false;
                            } else {
                                $redirect = $domain['domain'];
                            }
                            break;
                        }
                    }
                }
            } else if (Vps_Config::getValue('server.domain')
                && $host != Vps_Config::getValue('server.domain')
                && (!Vps_Config::getValue('server.noRedirectPattern') || !preg_match('/'.Vps_Config::getValue('server.noRedirectPattern').'/', $host))
            ) {
                $redirect = Vps_Config::getValue('server.domain');
            }
            if ($redirect) {
                $target = Vps_Model_Abstract::getInstance('Vps_Util_Model_Redirects')
                    ->findRedirectUrl('domainPath', array($host.$_SERVER['REQUEST_URI'], 'http://'.$host.$_SERVER['REQUEST_URI']));
                if (!$target) {
                    $target = Vps_Model_Abstract::getInstance('Vps_Util_Model_Redirects')
                        ->findRedirectUrl('domain', $host);
                }
                if ($target) {
                    //TODO: funktioniert nicht bei mehreren domains
                    header("Location: http://".$redirect.$target, true, 301);
                } else {
                    header("Location: http://".$redirect.$_SERVER['REQUEST_URI'], true, 301);
                }
                exit;
            }
        }

        if (isset($_SERVER['REQUEST_URI']) &&
            substr($_SERVER['REQUEST_URI'], 0, 14) == '/vps/util/apc/'
        ) {
            Vps_Util_Apc::dispatchUtils();
        }

        if (Vps_Config::getValue('showPlaceholder')
                && !Vps_Config::getValue('ignoreShowPlaceholder')
                && php_sapi_name() != 'cli'
                && isset($_SERVER['REQUEST_URI'])
                && substr($_SERVER['REQUEST_URI'], 0, 8)!='/assets/'
        ) {
            $view = new Vps_View();
            echo $view->render('placeholder.tpl');
            exit;
        }

        if (php_sapi_name() != 'cli' && Vps_Config::getValue('preLogin')
            && isset($_SERVER['REDIRECT_URL'])
        ) {
            $ignore = false;
            foreach (Vps_Config::getValueArray('preLoginIgnore') as $i) {
                if (substr($_SERVER['REDIRECT_URL'], 0, strlen($i)) == $i) {
                    $ignore = true;
                    break;
                }
            }
            foreach (Vps_Config::getValueArray('preLoginIgnoreIp') as $i) {
                if ($_SERVER['REMOTE_ADDR'] == $i) $ignore = true;
            }
            
            if (!$ignore && (empty($_SERVER['PHP_AUTH_USER'])
                             || empty($_SERVER['PHP_AUTH_PW'])
                             || $_SERVER['PHP_AUTH_USER']!=Vps_Config::getValue('preLoginUser')
                             || $_SERVER['PHP_AUTH_PW']!=Vps_Config::getValue('preLoginPassword'))
            ) {
                header('WWW-Authenticate: Basic realm="Testserver"');
                throw new Vps_Exception_AccessDenied();
            }
        }

        if ($tl = Vps_Config::getValue('debug.timeLimit')) {
            set_time_limit((int)$tl);
        }

        if (!isset($_SERVER['REDIRECT_URL']) ||
            (substr($_SERVER['REDIRECT_URL'], 0, 7) != '/media/'
             && substr($_SERVER['REDIRECT_URL'], 0, 8) != '/assets/'
             && substr($_SERVER['REDIRECT_URL'], 0, 7) != '/output') //rssinclude
        ) {
            self::_setLocale();
        }
        Vps_Benchmark::checkpoint('setUp');
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
        return file_exists('application/config.db.ini');
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
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        //www abschneiden damit www.test und www.preview usw auch funktionieren
        if (substr($host, 0, 4)== 'www.') $host = substr($host, 4);

        if (php_sapi_name() == 'cli') {
            //wenn über kommandozeile aufgerufen
            $path = getcwd();
        } else {
            $path = $_SERVER['SCRIPT_FILENAME'];
        }
        if (file_exists('application/config_section')) {
            return trim(file_get_contents('application/config_section'));
        } else if (file_exists('/var/www/vivid-test-server')) {
            return 'vivid-test-server';
        } else if (preg_match('#/(www|wwwnas)/(usr|public)/([0-9a-z-]+)/#', $path, $m)) {
            if ($m[3]=='vps-projekte') return 'vivid';
            return $m[3];
        } else if (substr($host, 0, 9)=='dev.test.') {
            return 'devtest';
        } else if (substr($host, 0, 4)=='dev.') {
            return 'dev';
        } else if (substr($host, 0, 5)=='test.' ||
                   substr($host, 0, 3)=='qa.' ||
                   substr($path, 0, 17) == '/docs/vpcms/test.' ||
                   substr($path, 0, 21) == '/docs/vpcms/www.test.' ||
                   substr($path, 0, 25) == '/var/www/html/vpcms/test.' ||
                   substr($path, 0, 20) == '/var/www/vpcms/test.') {
            return 'test';
        } else if (substr($host, 0, 8)=='preview.') {
            return 'preview';
        } else {
            return 'production';
        }
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
