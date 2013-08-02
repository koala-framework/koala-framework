<?php
class Kwf_Util_Setup
{
    public static function minimalBootstrapAndGenerateFile()
    {
        if (!defined('KWF_PATH')) define('KWF_PATH', realpath(dirname(__FILE__).'/../..'));
        if (file_exists(KWF_PATH.'/include_path')) {
            $zendPath = trim(file_get_contents(KWF_PATH.'/include_path'));
            $zendPath = str_replace(
                '%version%',
                file_get_contents(KWF_PATH.'/include_path_version'),
                $zendPath);

        } else {
            die ('zend not found');
        }

        //reset include path, don't use anything from php.ini
        set_include_path(get_include_path() . PATH_SEPARATOR . KWF_PATH . PATH_SEPARATOR . $zendPath);

        require_once 'Kwf/Loader.php';
        Kwf_Loader::registerAutoload();

        require_once 'Kwf/Registry.php';
        Zend_Registry::setClassName('Kwf_Registry');

        require_once 'Kwf/Trl.php';

        umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen

        file_put_contents('cache/setup'.Kwf_Setup::CACHE_SETUP_VERSION.'.php', self::generateCode());

        Zend_Registry::_unsetInstance(); //cache/setup?.php will call setClassName again
    }

    public static function generateCode()
    {
        $ip = get_include_path();
        $ip = explode(PATH_SEPARATOR, $ip);
        $ip[] = 'cache/generated';
        foreach (Kwf_Config::getValueArray('includepath') as $t=>$p) {
            $ip[] = $p;
        }
        $ip = array_unique($ip);

        $ret = "<?php\n";

        $preloadClasses = array(
            'Zend_Registry',
            'Kwf_Registry',
            'Kwf_Benchmark',
            'Kwf_Loader',
        );
        foreach ($preloadClasses as $cls) {
            foreach ($ip as $path) {
                $file = $path.'/'.str_replace('_', '/', $cls).'.php';
                if (file_exists($file)) {
                    $ret .= "require_once('".$file."');\n";
                    break;
                }
            }
        }

        $ret .= "Kwf_Benchmark::\$startTime = microtime(true);\n";
        $ret .= "\n";

        //only replace configured value to avoid spoofing
        //required eg. behind load balancers
        if (Kwf_Config::getValue('server.replaceVars.remoteAddr')) {
            $a = Kwf_Config::getValue('server.replaceVars.remoteAddr');
            $ret .= "\nif (isset(\$_SERVER['$a'])) \$_SERVER['REMOTE_ADDR'] = \$_SERVER['$a'];\n";
        }

        //try different values, if one spoofs this this is no security issue
        $ret .= "if (isset(\$_SERVER['HTTP_SSL_SESSION_ID'])) \$_SERVER['HTTPS'] = 'on';\n";
        $ret .= "if (isset(\$_SERVER['HTTP_SESSION_ID_TAG'])) \$_SERVER['HTTPS'] = 'on';\n";
        $ret .= "if (isset(\$_SERVER['HTTP_X_FORWARDED_PROTO']) && \$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {\n";
        $ret .= "    \$_SERVER['HTTPS'] = 'on';\n";
        $ret .= "}\n";

        $ret .= "if (!defined('KWF_PATH')) define('KWF_PATH', '".KWF_PATH."');\n";

        $ret .= "Kwf_Loader::setIncludePath('".implode(PATH_SEPARATOR, $ip)."');\n";
        $ret .= "\n";
        $ret .= "\n";
        $ret .= "Zend_Registry::setClassName('Kwf_Registry');\n";
        $ret .= "\n";
        $ret .= "//here to be as fast as possible (and have no session)\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 25) == '/kwf/json-progress-status'\n";
        $ret .= ") {\n";
        $ret .= "    require_once('Kwf/Util/ProgressBar/DispatchStatus.php');\n";
        $ret .= "    Kwf_Util_ProgressBar_DispatchStatus::dispatch();\n";
        $ret .= "}\n";
        $ret .= "\n";
        $ret .= "//here to have less dependencies\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 17) == '/kwf/check-config'\n";
        $ret .= ") {\n";
        $ret .= "    require_once('Kwf/Util/Check/Config.php');\n";
        $ret .= "    Kwf_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";
        $ret .= "if (php_sapi_name() == 'cli' && isset(\$_SERVER['argv'][1]) && \$_SERVER['argv'][1] == 'check-config') {\n";
        $ret .= "    require_once('Kwf/Util/Check/Config.php');\n";
        $ret .= "    Kwf_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";

        if (Kwf_Config::getValue('debug.benchmark')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Kwf_Benchmark::enable();\n";
        } else {
            $ret .= "if (isset(\$_REQUEST['KWF_BENCHMARK'])) {\n";
            foreach (Kwf_Config::getValueArray('debug.benchmarkActivatorIp') as $activatorIp) {
                $ret .= "    if (\$_SERVER['REMOTE_ADDR'] == '$activatorIp') Kwf_Benchmark::enable();\n";
            }
            $ret .= "}\n";
        }
        if (Kwf_Config::getValue('debug.benchmarkLog')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Kwf_Benchmark::enableLog();\n";
        }

        $ret .= "Kwf_Loader::registerAutoload();\n";

        $ret .= "\$ml = ini_get('memory_limit');\n";
        $ret .= "if (strtoupper(substr(\$ml, -1)) == 'M') {\n";
        $ret .= "    if ((int)substr(\$ml, 0, -1) < 128*1024*1024) {\n";
        $ret .= "        ini_set('memory_limit', '128M');\n";
        $ret .= "    }\n";
        $ret .= "}\n";
        $ret .= "error_reporting(E_ALL & ~E_STRICT);\n";
        $ret .= "date_default_timezone_set('Europe/Berlin');\n";
        if (function_exists('mb_internal_encoding')) {
            $ret .= "mb_internal_encoding('UTF-8');\n";
        }
        if (function_exists('iconv_set_encoding')) {
            $ret .= "iconv_set_encoding('internal_encoding', 'utf-8');\n";
        }
        $ret .= "set_error_handler(array('Kwf_Debug', 'handleError'), E_ALL & ~E_STRICT);\n";
        $ret .= "set_exception_handler(array('Kwf_Debug', 'handleException'));\n";
        $ret .= "umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen\n";

        $ret .= "Zend_Registry::set('requestNum', ''.floor(Kwf_Benchmark::\$startTime*100));\n";

        if (Kwf_Config::getValue('debug.firephp') || Kwf_Config::getValue('debug.querylog')) {
            $ret .= "if (php_sapi_name() != 'cli') {\n";
            if (Kwf_Config::getValue('debug.firephp')) {
                $ret .= "    require_once '".Kwf_Config::getValue('externLibraryPath.firephp')."/FirePHPCore/FirePHP.class.php';\n";
                $ret .= "    FirePHP::init();\n";
            }

            if (Kwf_Config::getValue('debug.querylog')) {
                $ret .= "    header('X-Kwf-RequestNum: '.Zend_Registry::get('requestNum'));\n";
                $ret .= "    register_shutdown_function(array('Kwf_Setup', 'shutDown'));\n";
            }
            $ret .= "    ob_start();\n";
            $ret .= "}\n";
        }

        $preloadClasses = array(
            'Kwf_Loader',
            'Kwf_Config',
            'Kwf_Cache_Simple',
            'Kwf_Debug',
            'Kwf_Trl',
        );
        if (Kwf_Component_Data_Root::getComponentClass()) {
            //only load component related classes if it is a component web
            $preloadClasses[] = 'Kwf_Model_Select';
            $preloadClasses[] = 'Kwf_Component_Data';
            $preloadClasses[] = 'Kwf_Component_Data_Root';
            $preloadClasses[] = 'Kwf_Component_Select';
            $preloadClasses[] = 'Kwf_Component_Abstract';
            $preloadClasses[] = 'Kwc_Abstract';
            $preloadClasses[] = 'Kwc_Paragraphs_Component';
            $preloadClasses[] = 'Kwf_Component_Renderer_Abstract';
            $preloadClasses[] = 'Kwf_Component_Renderer';
            $preloadClasses[] = 'Kwf_Component_Cache';
            $preloadClasses[] = 'Kwf_Component_Cache_Mysql';
            $preloadClasses[] = 'Kwf_Component_View_Helper_Abstract';
            $preloadClasses[] = 'Kwf_Component_View_Renderer';
            $preloadClasses[] = 'Kwf_Component_View_Helper_Master';
            $preloadClasses[] = 'Kwf_Component_View_Helper_Component';
            $preloadClasses[] = 'Kwf_Component_View_Helper_ComponentLink';
            $preloadClasses[] = 'Kwf_View_Helper_Link';
            $preloadClasses[] = 'Kwf_Component_Abstract_ContentSender_Abstract';
            $preloadClasses[] = 'Kwf_Component_Abstract_ContentSender_Default';
        }
        foreach ($preloadClasses as $cls) {
            foreach ($ip as $path) {
                $file = $path.'/'.str_replace('_', '/', $cls).'.php';
                if (file_exists($file)) {
                    $ret .= "require_once('".$file."');\n";
                    break;
                }
            }
        }

        $ret .= "\$host = isset(\$_SERVER['HTTP_HOST']) ? \$_SERVER['HTTP_HOST'] : null;\n";

        $configSection = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
        $ret .= "Kwf_Setup::\$configSection = '".$configSection."';\n";

        if (Kwf_Config::getValue('debug.checkBranch')) {
            $ret .= "if (is_file('kwf_branch') && trim(file_get_contents('kwf_branch')) != Kwf_Config::getValue('application.kwf.version')) {\n";
            $ret .= "    \$validCommands = array('shell', 'export', 'copy-to-test');\n";
            $ret .= "    if (php_sapi_name() != 'cli' || !isset(\$_SERVER['argv'][1]) || !in_array(\$_SERVER['argv'][1], \$validCommands)) {\n";
            $ret .= "        \$required = trim(file_get_contents('kwf_branch'));\n";
            $ret .= "        \$kwfBranch = Kwf_Util_Git::kwf()->getActiveBranch();\n";
            $ret .= "        throw new Kwf_Exception_Client(\"Invalid Kwf branch. Required: '\$required', used: '\".Kwf_Config::getValue('application.kwf.version').\"' (Git branch '\$kwfBranch')\");\n";
            $ret .= "    }\n";
            $ret .= "}\n";
        }

        //store session data in memcache if avaliable
        if ((Kwf_Config::getValue('server.memcache.host') || Kwf_Config::getValue('aws.simpleCacheCluster')) && Kwf_Setup::hasDb()) {
            $ret .= "\nif (php_sapi_name() != 'cli') Kwf_Util_SessionHandler::init();\n";
        }

        $ret .= "if (isset(\$_POST['PHPSESSID'])) {\n";
        $ret .= "    //für swfupload\n";
        $ret .= "    Zend_Session::setId(\$_POST['PHPSESSID']);\n";
        $ret .= "}\n";

        /*
        if (isset($_COOKIE['unitTest'])) {
            //$config->debug->benchmark = false;
        }
        */

        if (!Kwf_Config::getValue('server.domain')) {
            //hack to make clear-cache just work
            $ret .= "if (\$host) file_put_contents('cache/lastdomain', \$host);\n";
        }

        //up here to have less dependencies or broken redirect
        $ret .= "\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 14) == '/kwf/util/apc/'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_Apc::dispatchUtils();\n";
        $ret .= "}\n";

        // Falls redirectToDomain eingeschalten ist, umleiten
        if (Kwf_Config::getValue('server.redirectToDomain')) {
            $ret .= "if (\$host) {\n";
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

        if (Kwf_Config::getValue('showPlaceholder')) {
            $ret .= "if (php_sapi_name() != 'cli' && Kwf_Setup::getRequestPath() && substr(Kwf_Setup::getRequestPath(), 0, 8)!='/assets/') {\n";
            $ret .= "    \$view = new Kwf_View();\n";
            $ret .= "    echo \$view->render('placeholder.tpl');\n";
            $ret .= "    exit;\n";
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

        $locale = Kwf_Trl::getInstance()->trlc('locale', 'C', array(), Kwf_Trl::SOURCE_KWF, Kwf_Trl::getInstance()->getWebCodeLanguage());
        $ret .= "setlocale(LC_ALL, explode(', ', '".$locale."'));\n";
        $ret .= "setlocale(LC_NUMERIC, 'C');\n";

        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    (substr(\$_SERVER['REQUEST_URI'], 0, 9) == '/kwf/pma/' || \$_SERVER['REQUEST_URI'] == '/kwf/pma')\n";
        $ret .= ") {\n";
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

        $ret .= "Kwf_Benchmark::checkpoint('setUp');\n";
        $ret .= "\n";

        return $ret;
    }
}
