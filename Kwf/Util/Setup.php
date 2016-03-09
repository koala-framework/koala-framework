<?php
class Kwf_Util_Setup
{
    private static function _getZendPath()
    {
        return VENDOR_PATH.'/koala-framework/zendframework1/library';
    }

    public static function minimalBootstrap()
    {
        if (!defined('VENDOR_PATH')) define('VENDOR_PATH', 'vendor');
        if (VENDOR_PATH == '../vendor') {
            $kwfPath = '..';
        } else {
            $kwfPath = VENDOR_PATH.'/koala-framework/koala-framework';
        }
        if (!defined('KWF_PATH')) define('KWF_PATH', $kwfPath);

        //reset include path, don't use anything from php.ini
        set_include_path('.' . PATH_SEPARATOR . $kwfPath . PATH_SEPARATOR . self::_getZendPath());

        require_once $kwfPath.'/Kwf/Loader.php';
        Kwf_Loader::registerAutoload();

        Zend_Registry::setClassName('Kwf_Registry');

        $configSection = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
        Kwf_Setup::$configSection = $configSection;

        error_reporting(E_ALL^E_STRICT);

        class_exists('Kwf_Trl'); //trigger autoload

        umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen
    }

    public static function minimalBootstrapAndGenerateFile()
    {
        self::minimalBootstrap();

        file_put_contents('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php', self::generateCode());

        Zend_Registry::_unsetInstance(); //cache/setup?.php will call setClassName again
    }

    private static function _verifyPathInParentPath($path, $parentPath)
    {
        $path = realpath($path);
        $parentPath = realpath($parentPath);
        if (substr($path, 0, strlen($parentPath)) != $parentPath) {
            throw new Kwf_Exception("'$path' not in '$parentPath'");
        }
    }

    private static function _generatePreloadClassesCode($preloadClasses, $ip)
    {
        $ret = '';
        foreach ($preloadClasses as $cls) {
            foreach ($ip as $path) {
                $file = $path.'/'.str_replace('_', '/', $cls).'.php';
                if (file_exists($file)) {
                    if (VENDOR_PATH == '../vendor') {
                        $cwd = getcwd();
                        $cwd = substr($cwd, 0, strrpos($cwd, '/'));
                        self::_verifyPathInParentPath($file, $cwd);
                        $file = '../'.substr($file, strlen($cwd)+1);
                    } else {
                        self::_verifyPathInParentPath($file, getcwd());
                        $file = substr($file, strlen(getcwd())+1);
                    }
                    $ret .= "require(\$cwd.'/".$file."');\n";
                    break;
                }
            }
        }
        return $ret;
    }

    public static function generateCode()
    {
        $preloadIncludePaths = array();
        $namespaces = require VENDOR_PATH.'/composer/autoload_namespaces.php';
        $ret = array();
        foreach ($namespaces as $ns=>$dirs) {
            $preloadIncludePaths = array_merge($preloadIncludePaths, $dirs);
        }

        $ret = "<?php\n";

        $ret .= "\$cwd = getcwd();\n";

        $preloadClasses = array(
            'Kwf_Benchmark',
            'Kwf_Loader',
            'Kwf_Debug',
        );
        $ret .= "if (!class_exists('Kwf_Loader', false)) {\n";
        $ret .= self::_generatePreloadClassesCode($preloadClasses, $preloadIncludePaths);
        $ret .= "}\n";

        $ret .= "Kwf_Benchmark::\$startTime = microtime(true);\n";
        $ret .= "\n";

        //override the default cache (/tmp) dir used by Zend_Cache_Backend to have the cache per web
        $ret .= "\$_SERVER['TMP'] = 'cache/zend';\n";

        //only replace configured value to avoid spoofing
        //required eg. behind load balancers
        if (Kwf_Config::getValueArray('server.replaceVars.remoteAddr')) {
            $a = Kwf_Config::getValueArray('server.replaceVars.remoteAddr');
            if (substr($a['if'], -2) == '.*') {
                $comparison = "substr(\$_SERVER['REMOTE_ADDR'], 0, ".(strlen($a['if'])-1).") == '".substr($a['if'], 0, -1)."'";
            } else {
                $comparison = "\$_SERVER['REMOTE_ADDR'] == '$a[if]'";
            }
            $ret .= "\nif (isset(\$_SERVER['REMOTE_ADDR']) && $comparison && isset(\$_SERVER['$a[replace]'])) {\n";
            $ret .= "    \$_SERVER['REMOTE_ADDR'] = \$_SERVER['$a[replace]'];\n";
            if (isset($a['removeTrailing'])) {
                $ret .= "    if (substr(\$_SERVER['REMOTE_ADDR'], -".strlen($a['removeTrailing']).") == '".$a['removeTrailing']."') {\n";
                $ret .= "        \$_SERVER['REMOTE_ADDR'] = substr(\$_SERVER['REMOTE_ADDR'], 0, -".strlen($a['removeTrailing']).");\n";
                $ret .= "    }\n";
            }
            $ret .= "}\n";
        }

        //try different values, if one spoofs this this is no security issue
        $ret .= "if (isset(\$_SERVER['HTTP_SSL_SESSION_ID'])) \$_SERVER['HTTPS'] = 'on';\n";
        $ret .= "if (isset(\$_SERVER['HTTP_SESSION_ID_TAG'])) \$_SERVER['HTTPS'] = 'on';\n";
        $ret .= "if (isset(\$_SERVER['HTTP_X_FORWARDED_PROTO']) && \$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {\n";
        $ret .= "    \$_SERVER['HTTPS'] = 'on';\n";
        $ret .= "}\n";

        if (VENDOR_PATH == '../vendor') {
            $kwfPath = '..';
        } else {
            $kwfPath = VENDOR_PATH.'/koala-framework/koala-framework';
        }
        $ret .= "if (!defined('KWF_PATH')) define('KWF_PATH', '$kwfPath');\n";
        $ret .= "if (!defined('VENDOR_PATH')) define('VENDOR_PATH', 'vendor');\n";

        $ip = array();
        foreach (include VENDOR_PATH.'/composer/include_paths.php' as $p) {
            if (VENDOR_PATH == '../vendor') {
                $cwd = getcwd();
                $cwd = substr($cwd, 0, strrpos($cwd, '/'));
                self::_verifyPathInParentPath($p, $cwd);
                $p = '../'.substr($p, strlen($cwd)+1);
            } else {
                self::_verifyPathInParentPath($p, getcwd());
                $p = substr($p, strlen(getcwd())+1);
            }
            $ip[] = "'.\$cwd.'/".$p;
        }
        $ip[] = '.';
        foreach (Kwf_Config::getValueArray('includepath') as $t=>$p) {
            if ($p) $ip[] = $p;
        }
        $ip = array_unique($ip);
        $ret .= "Kwf_Loader::setIncludePath('".implode(PATH_SEPARATOR, $ip)."');\n";
        $ret .= "\n";
        $ret .= "\n";
        $ret .= "error_reporting(E_ALL & ~E_STRICT);\n";
        $ret .= "set_error_handler(array('Kwf_Debug', 'handleError'), E_ALL & ~E_STRICT);\n";
        $ret .= "set_exception_handler(array('Kwf_Debug', 'handleException'));\n";
        $ret .= "\n";
        $ret .= "\$requestUri = isset(\$_SERVER['REQUEST_URI']) ? \$_SERVER['REQUEST_URI'] : null;\n";
        if (Kwf_Setup::getBaseUrl()) {
            $ret .= "if (\$requestUri !== null) {\n";
            $ret .= "    if (substr(\$requestUri, 0, ".strlen(Kwf_Setup::getBaseUrl()).") != '".Kwf_Setup::getBaseUrl()."') {\n";
            $ret .= "        echo 'Invalid baseUrl, expected \'".Kwf_Setup::getBaseUrl()."\'';\n";
            $ret .= "        exit;\n";
            $ret .= "    }\n";
            $ret .= "    \$requestUri = substr(\$requestUri, ".strlen(Kwf_Setup::getBaseUrl()).");\n";
            $ret .= "}\n";
        }
        $ret .= "\n";

        if (Kwf_Config::getValue('debug.benchmark') || Kwf_Config::getValue('debug.benchmarklog')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Kwf_Benchmark::enable();\n";
        } else {
            $ret .= "if (isset(\$_REQUEST['KWF_BENCHMARK'])) {\n";
            foreach (Kwf_Config::getValueArray('debug.benchmarkActivatorIp') as $activatorIp) {
                $ret .= "    if (\$_SERVER['REMOTE_ADDR'] == '$activatorIp') Kwf_Benchmark::enable();\n";
            }
            $ret .= "}\n";
        }

        $ret .= "Kwf_Loader::registerAutoload();\n";
        $ret .= "\n";

        $configSection = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
        $ret .= "Kwf_Setup::\$configSection = '".$configSection."';\n";

        $ret .= "//here to be as fast as possible (and have no session)\n";
        $ret .= "if (\$requestUri == '/kwf/json-progress-status'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_ProgressBar_DispatchStatus::dispatch();\n";
        $ret .= "}\n";
        $ret .= "\n";
        $ret .= "//here to have less dependencies\n";
        $ret .= "if (\$requestUri == '/kwf/check-config'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";
        $ret .= "if (php_sapi_name() == 'cli' && isset(\$_SERVER['argv'][1]) && \$_SERVER['argv'][1] == 'check-config') {\n";
        $ret .= "    Kwf_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";

        $ret .= "\$ml = ini_get('memory_limit');\n";
        $ret .= "if (strtoupper(substr(\$ml, -1)) == 'M') {\n";
        $ret .= "    if ((int)substr(\$ml, 0, -1) < 128) {\n";
        $ret .= "        ini_set('memory_limit', '128M');\n";
        $ret .= "    }\n";
        $ret .= "}\n";

        if (Kwf_Config::getValue('debug.error.log')) {
            $ret .= "ini_set('display_errors', false);\n";
        }

        if (function_exists('mb_internal_encoding')) {
            $ret .= "mb_internal_encoding('UTF-8');\n";
        }
        $ret .= "ini_set('default_charset', 'UTF-8');\n";
        $ret .= "umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen\n";

        //this is *NOT* recommended but still works somehow
        $ret .= "if (get_magic_quotes_gpc()) Kwf_Util_UndoMagicQuotes::undoMagicQuotes();\n";

        if (Kwf_Config::getValue('debug.firephp') || Kwf_Config::getValue('debug.querylog')) {
            $ret .= "if (php_sapi_name() != 'cli') {\n";
            if (Kwf_Config::getValue('debug.firephp')) {
                $ret .= "    require_once '".Kwf_Config::getValue('externLibraryPath.firephp')."/FirePHPCore/FirePHP.class.php';\n";
                $ret .= "    FirePHP::init();\n";
                $ret .= "    ob_start();\n";
            }
            $ret .= "}\n";
        }
        $ret .= "register_shutdown_function(array('Kwf_Setup', 'shutDown'));\n";


        $ret .= "if (!class_exists('Kwf_Config', false)) {\n";
        $preloadClasses = array(
            'Kwf_Config',
            'Kwf_Cache_Simple',
            'Kwf_Cache_SimpleStatic',
        );
        $ret .= self::_generatePreloadClassesCode($preloadClasses, $preloadIncludePaths);

        $ret .= "    if (substr(\$requestUri, 0, 8) != '/assets/') {\n";
        $preloadClasses = array();
        $preloadClasses[] = 'Zend_Registry';
        $preloadClasses[] = 'Kwf_Registry';
        $preloadClasses[] = 'Kwf_Trl';
        $preloadClasses[] = 'Kwf_Util_SessionHandler';
        $preloadClasses[] = 'Kwf_Util_Memcache';
        $preloadClasses[] = 'Zend_Session';
        $preloadClasses[] = 'Kwf_Benchmark_Counter';
        $preloadClasses[] = 'Kwf_Benchmark_Counter_Apc';

        if (Kwf_Component_Data_Root::getComponentClass()) {
            //only load component related classes if it is a component web
            $preloadClasses[] = 'Kwf_Component_Data';
            $preloadClasses[] = 'Kwf_Component_Data_Root';
            $preloadClasses[] = 'Kwf_Component_Settings';

            $preloadClasses[] = 'Kwf_Component_Renderer_Abstract';
            $preloadClasses[] = 'Kwf_Component_Renderer';
            $preloadClasses[] = 'Kwf_Component_Cache';
            $preloadClasses[] = 'Kwf_Component_Cache_Mysql';
            $preloadClasses[] = 'Kwf_Component_Cache_Memory';
            $preloadClasses[] = 'Kwf_Component_Abstract_ContentSender_Abstract';
            $preloadClasses[] = 'Kwf_Component_Abstract_ContentSender_Default';
        }
        $ret .= self::_generatePreloadClassesCode($preloadClasses, $preloadIncludePaths);
        $ret .= "    } else {\n";
        $preloadClasses = array();
        $preloadClasses[] = 'Kwf_Assets_Loader';
        $preloadClasses[] = 'Kwf_Media_Output';
        $ret .= self::_generatePreloadClassesCode($preloadClasses, $preloadIncludePaths);
        $ret .= "    }\n";
        $ret .= "}\n";

        Kwf_Cache_Simple::$backend = null; //unset to re-calculate
        $ret .= "Kwf_Cache_Simple::\$backend = '".Kwf_Cache_Simple::getBackend()."';\n";

        if (Kwf_Config::getValue('server.memcache.host')) {
            $host = Kwf_Config::getValue('server.memcache.host');
            if ($host == '%webserverHostname%') {
                if (php_sapi_name() == 'cli') {
                    $host = Kwf_Util_Apc::callUtil('get-hostname', array(), array('returnBody'=>true, 'skipCache'=>true));
                } else {
                    $host = php_uname('n');
                }
            }
            $ret .= "Kwf_Cache_Simple::\$memcacheHost = '".$host."';\n";
            $ret .= "Kwf_Cache_Simple::\$memcachePort = '".Kwf_Config::getValue('server.memcache.port')."';\n";
        }

        $ret .= "if (substr(\$requestUri, 0, 8) == '/assets/') {\n";
        $ret .= "    \$url = \$requestUri;\n";
        $ret .= "    if (strpos(\$url, '?') !== false) {\n";
        $ret .= "        \$url = substr(\$url, 0, strpos(\$url, '?'));\n";
        $ret .= "    }\n";
        $dispatcherClass = Kwf_Config::getValue('assets.dispatcherClass');
        if (!$dispatcherClass) $dispatcherClass = 'Kwf_Assets_Dispatcher';
        $ret .= "    \$dispatcher = new ".$dispatcherClass."();\n";
        $ret .= "    \$dispatcher->dispatch(\$url);\n";
        $ret .= "    Kwf_Assets_Loader::load(\$url);\n";
        $ret .= "}\n";

        if (Kwf_Config::getValue('debug.benchmarkCounter')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Kwf_Benchmark::enableLog();\n";
        }
        $ret .= "Zend_Registry::setClassName('Kwf_Registry');\n";

        $ret .= "\$host = isset(\$_SERVER['HTTP_HOST']) ? \$_SERVER['HTTP_HOST'] : null;\n";

        $ret .= "session_name('SESSION_".Kwf_Config::getValue('application.id')."');\n";

        //up here to have less dependencies or broken redirect
        $ret .= "\n";
        $ret .= "if (substr(\$requestUri, 0, 14) == '/kwf/util/apc/'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_Apc::dispatchUtils();\n";
        $ret .= "}\n";


        if (Kwf_Config::getValue('server.https') !== 'unknown') {
            $redirectHttpsCode  = "    if (\$_SERVER['REQUEST_METHOD'] != 'GET') {\n";
            $redirectHttpsCode .= "        header('HTTP/1.1 400 Bad Request');\n";
            $redirectHttpsCode .= "        echo 'Invalid protocol, https required';\n";
            $redirectHttpsCode .= "        exit;\n";
            $redirectHttpsCode .= "    }\n";
            $redirectHttpsCode .= "    \$redirect = 'https://'.\$_SERVER['HTTP_HOST'].\$_SERVER['REQUEST_URI'];\n";
            $redirectHttpsCode .= "    header('Location: '.\$redirect, true, 301);\n";
            $redirectHttpsCode .= "    Kwf_Benchmark::shutDown();\n";
            $redirectHttpsCode .= "    exit;\n";
            $redirectHttpCode = str_replace('https', 'http', $redirectHttpsCode);

            $ret .= "if (PHP_SAPI != 'cli') {\n";
            if (!Kwf_Config::getValue('server.https')) {
                $ret .= "if (isset(\$_SERVER['HTTPS'])) {\n";
                $ret .= "    $redirectHttpCode";
                $ret .= "}\n";
            } else {
                if ($domains = Kwf_Config::getValueArray('server.httpsDomains')) {
                    $ret .= "\$domains = array(";
                    foreach ($domains as $d) {
                        $ret .= "'".$d."'=>true, ";
                    }
                    $ret .= ");\n";
                    $ret .= "\$supportsHttps = isset(\$_SERVER['HTTP_HOST']) && isset(\$domains[\$_SERVER['HTTP_HOST']]);\n";
                    $ret .= "if (\$supportsHttps != isset(\$_SERVER['HTTPS'])) {\n";
                    $ret .= "    if (\$supportsHttps) {\n";
                    $ret .= "        $redirectHttpsCode";
                    $ret .= "    } else {\n";
                    $ret .= "        $redirectHttpCode";
                    $ret .= "    }\n";
                    $ret .= "}\n";
                } else {
                    $ret .= "if (!isset(\$_SERVER['HTTPS'])) {\n";
                    $ret .= "$redirectHttpsCode";
                    $ret .= "}\n";
                }
            }
            $ret .= "}\n";
        }
        
        $ret .= "session_set_cookie_params(\n";
        $ret .= " 0,";     //lifetime
        $ret .= " '".Kwf_Setup::getBaseUrl()."/',";   //path
        $ret .= " null,";  //domain
        $ret .= " isset(\$_SERVER['HTTPS']),"; //secure
        $ret .= " true";   //httponly
        $ret .= ");\n";

        $ret .= "\n";

        //store session data in memcache if avaliable
        if ((Kwf_COnfig::getValue('server.memcache.host') || Kwf_Config::getValue('aws.simpleCacheCluster')) && Kwf_Setup::hasDb()) {
            $ret .= "\nif (php_sapi_name() != 'cli') Kwf_Util_SessionHandler::init();\n";
        }

        // Falls redirectToDomain eingeschalten ist, umleiten
        if (Kwf_Config::getValue('server.redirectToDomain')) {
            $ret .= "if (\$host && substr(\$requestUri, 0, 17) != '/kwf/maintenance/' && substr(\$requestUri, 0, 8) != '/assets/') {\n";
            $ret .= "    \$redirect = false;\n";
            if ($domains = Kwf_Config::getValueArray('kwc.domains')) {
                $ret .= "    \$domainMatches = false;\n";
                foreach ($domains as $domain) {
                    $ret .= "    if ('{$domain['domain']}' == \$host) \$domainMatches = true;\n";
                }
                $ret .= "    if (!\$domainMatches) {\n";
                foreach ($domains as $domain) {
                    if (isset($domain['pattern'])) {
                        $ret .= "\n";
                        $ret .= "        //pattern\n";
                        $ret .= "        if (!\$domainMatches && preg_match('/{$domain['pattern']}/', \$host)) {\n";
                        $ret .= "            \$domainMatches = true;\n";
                        if (isset($domain['noRedirectPattern'])) {
                            $ret .= "\n";
                            $ret .= "            //noRedirectPattern\n";
                            $ret .= "            if (!preg_match('/{$domain['noRedirectPattern']}/', \$host)) {\n";
                            $ret .= "                \$redirect = '{$domain['domain']}';\n";
                            $ret .= "            }\n";
                        } else {
                            $ret .= "            \$redirect = '{$domain['domain']}';\n";
                        }
                        $ret .= "        }\n";
                    }
                }
                $ret .= "    }\n";
                $ret .= "    if (!\$domainMatches) {\n";
                $ret .= "        \$redirect = '".Kwf_Config::getValue('server.domain')."';\n";
                $ret .= "    }\n";
            } else if (Kwf_Config::getValue('server.domain')) {
                $ret .= "    if (\$host != '".Kwf_Config::getValue('server.domain')."') {\n";
                    if (Kwf_Config::getValue('server.noRedirectPattern')) {
                        $ret .= "        if (!preg_match('/".Kwf_Config::getValue('server.noRedirectPattern')."/', \$host)) {\n";
                        $ret .= "            \$redirect = '".Kwf_Config::getValue('server.domain')."';\n";
                        $ret .= "        }\n";
                    } else {
                        $ret .= "        \$redirect = '".Kwf_Config::getValue('server.domain')."';\n";
                    }
                $ret .= "    }\n";
            }
            $ret .= "    if (\$redirect) {\n";
            $ret .= "        \$target = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Redirects')\n";
            $ret .= "            ->findRedirectUrl('domainPath', \$host.\$_SERVER['REQUEST_URI']);\n";
            $ret .= "        if (!\$target) {\n";
            $ret .= "            \$target = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Redirects')\n";
            $ret .= "                ->findRedirectUrl('domain', \$host);\n";
            $ret .= "        }\n";
            $ret .= "        if (\$target) {\n";
            $ret .= "            header('Location: '.\$target, true, 301);\n";
            $ret .= "        } else {\n";
            $ret .= "            //redirect to main domain (server.domain)\n";
            $ret .= "            header('Location: http://'.\$redirect.\$_SERVER['REQUEST_URI'], true, 301);\n";
            $ret .= "        }\n";
            $ret .= "        exit;\n";
            $ret .= "    }\n";
            $ret .= "}\n";
        }

        if (Kwf_Config::getValue('preLogin')) {
            $ret .= "if (php_sapi_name() != 'cli' && Kwf_Setup::getRequestPath()!==false) {\n";
            $ret .= "    \$ignore = false;\n";
            foreach (Kwf_Config::getValueArray('preLoginIgnore') as $i) {
                $ret .= "    if (substr(\$_SERVER['REDIRECT_URL'], 0, ".strlen($i).") == '$i') \$ignore = true;\n";
            }
            foreach (Kwf_Config::getValueArray('preLoginIgnoreIp') as $i) {
                if (substr($i, -1)=='*') {
                    $i = substr($i, 0, -1);
                    $ret .= "    if (substr(\$_SERVER['REMOTE_ADDR'], 0, ".strlen($i).") == '$i') \$ignore = true;\n";
                } else if (substr($i, 0, 1)=='*') {
                    $i = substr($i, 1);
                    $ret .= "    if (substr(\$_SERVER['REMOTE_ADDR'], -".strlen($i).") == '$i') \$ignore = true;\n";
                } else {
                    $ret .= "    if (\$_SERVER['REMOTE_ADDR'] == '$i') \$ignore = true;\n";
                }
            }

            $ret .= "    if (!\$ignore && (empty(\$_SERVER['PHP_AUTH_USER'])\n";
            $ret .= "           || empty(\$_SERVER['PHP_AUTH_PW'])\n";
            $ret .= "            || \$_SERVER['PHP_AUTH_USER']!='".Kwf_Config::getValue('preLoginUser')."'\n";
            $ret .= "           || \$_SERVER['PHP_AUTH_PW']!='".Kwf_Config::getValue('preLoginPassword')."')\n";
            $ret .= "    ) {\n";
            $ret .= "        \$realm = 'Testserver';\n";
            $ret .= "        header('WWW-Authenticate: Basic realm=\"'.\$realm.'\"');\n";
            $ret .= "        throw new Kwf_Exception_AccessDenied();\n";
            $ret .= "    }\n";
            $ret .= "}\n";
        }

        if ($parameters = Kwf_Config::getValueArray('parameterToCookie')) {
            foreach($parameters as $parameter) {
                $ret .= "if (isset(\$_GET['".$parameter."'])) setcookie('".$parameter."', \$_GET['".$parameter."'], 0, '/');\n";
            }
        }

        if ($tl = Kwf_Config::getValue('debug.timeLimit')) {
            $ret .= "set_time_limit($tl);\n";
        }

        $ret .= "if (substr(\$requestUri, 0, 9) == '/kwf/pma/' || \$requestUri == '/kwf/pma') {\n";
        $ret .= "    Kwf_Util_Pma::dispatch();\n";
        $ret .= "}\n";

        $ret .= "if (isset(\$_GET['kwcPreview'])) {\n";
        $ret .= "    \$role = Kwf_Registry::get('userModel')->getAuthedUserRole();\n";
        $ret .= "    if (!Kwf_Registry::get('acl')->isAllowed(\$role, 'kwf_component_preview', 'view')) {\n";
        $ret .= "        header('Location: /admin/component/preview/redirect/?url='.urlencode(\$_SERVER['REQUEST_URI']));\n";
        $ret .= "        exit;\n";
        $ret .= "    }\n";
        $ret .= "    Kwf_Component_Data_Root::setShowInvisible(true);\n";
        $ret .= "}\n";

        return $ret;
    }
}
