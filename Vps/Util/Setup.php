<?php
class Vps_Util_Setup
{
    public static function generateCode($configClass)
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


        $ret = "<?php\n";
        $ret .= "require_once('Vps/Benchmark.php');\n";
        $ret .= "Vps_Benchmark::\$startTime = microtime(true);\n";
        $ret .= "\n";
        $ret .= "if (isset(\$_SERVER['HTTP_CLIENT_IP'])) \$_SERVER['REMOTE_ADDR'] = \$_SERVER['HTTP_CLIENT_IP'];\n";
        $ret .= "\n";

        $ip = get_include_path();
        $ip .= PATH_SEPARATOR . $zendPath;
        foreach (Vps_Config::getValueArray('includepath') as $t=>$p) {
            if ($t == 'phpunit') {
                //vorne anh�ngen damit er vorrang vor /usr/share/php hat
                $ip = $p . PATH_SEPARATOR . $ip;
            } else {
                $ip .= PATH_SEPARATOR . $p;
            }
        }
        $ret .= "set_include_path('$ip');\n";
        $ret .= "\n";
        $ret .= "require_once 'Vps/Loader.php';\n";
        $ret .= "\n";
        $ret .= "\n";
        $ret .= "//here to be as fast as possible (and have no session)\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 25) == '/vps/json-progress-status'\n";
        $ret .= ") {\n";
        $ret .= "    Vps_Util_ProgressBar_DispatchStatus::dispatch();\n";
        $ret .= "}\n";
        $ret .= "\n";
        $ret .= "//here to have less dependencies\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 17) == '/vps/check-config'\n";
        $ret .= ") {\n";
        $ret .= "    Vps_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";
        $ret .= "if (php_sapi_name() == 'cli' && isset(\$_SERVER['argv'][1]) && \$_SERVER['argv'][1] == 'check-config') {\n";
        $ret .= "    Vps_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";
        $ret .= "\n";
        $ret .= "require_once 'Vps/Registry.php';\n";
        $ret .= "Zend_Registry::setClassName('Vps_Registry');\n";
        $ret .= "\n";
        $ret .= "Vps_Setup::\$configClass = '$configClass';\n";
        $ret .= "\n";
        if (Vps_Config::getValue('debug.componentCache.checkComponentModification')) {
            $ret .= "require_once 'Vps/Config.php';\n";
            $ret .= "Vps_Config::checkMasterFiles();\n";
        }

        if (Vps_Config::getValue('debug.benchmark')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Vps_Benchmark::enable();\n";
        }
        if (Vps_Config::getValue('debug.benchmarkLog')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Vps_Benchmark::enableLog();\n";
        }

        $ret .= "Vps_Loader::registerAutoload();\n";


        $ret .= "require_once 'Vps/Debug.php';\n";
        $ret .= "require_once 'Vps/Trl.php';\n";
        $ret .= "ini_set('memory_limit', '128M');\n";
        $ret .= "error_reporting(E_ALL);\n";
        $ret .= "date_default_timezone_set('Europe/Berlin');\n";
        $ret .= "mb_internal_encoding('UTF-8');\n";
        $ret .= "iconv_set_encoding('internal_encoding', 'utf-8');\n";
        $ret .= "set_error_handler(array('Vps_Debug', 'handleError'), E_ALL);\n";
        $ret .= "set_exception_handler(array('Vps_Debug', 'handleException'));\n";
        $ret .= "umask(000); //nicht 002 weil wwwrun und vpcms in unterschiedlichen gruppen\n";


        $ret .= "Zend_Registry::set('requestNum', ''.floor(microtime(true)*100));\n";

        if (Vps_Config::getValue('debug.firephp') || Vps_Config::getValue('debug.querylog')) {
            $ret .= "if (php_sapi_name() != 'cli') {\n";
            if (Vps_Config::getValue('debug.firephp')) {
                $ret .= "    require_once 'FirePHPCore/FirePHP.class.php';\n";
                $ret .= "    FirePHP::init();\n";
            }

            if (Vps_Config::getValue('debug.querylog')) {
                $ret .= "    header('X-Vps-RequestNum: '.Zend_Registry::get('requestNum'));\n";
                $ret .= "    register_shutdown_function(array('Vps_Setup', 'shutDown'));\n";
            }
            $ret .= "    ob_start();\n";
            $ret .= "}\n";
        }

        /*
        //TODO
        if (is_file('application/vps_branch') && trim(file_get_contents('application/vps_branch')) != Vps_Config::getValue('application.vps.version')) {
            $validCommands = array('shell', 'export', 'copy-to-test');
            if (php_sapi_name() != 'cli' || !isset($_SERVER['argv'][1]) || !in_array($_SERVER['argv'][1], $validCommands)) {
                $required = trim(file_get_contents('application/vps_branch'));
                $vpsBranch = Vps_Util_Git::vps()->getActiveBranch();
                throw new Vps_Exception_Client("Invalid Vps branch. Required: '$required', used: '".Vps_Config::getValue('application.vps.version')."' (Git branch '$vpsBranch')");
            }
        }
        */

        $ret .= "if (isset(\$_POST['PHPSESSID'])) {\n";
        $ret .= "    //für swfupload\n";
        $ret .= "    Zend_Session::setId(\$_POST['PHPSESSID']);\n";
        $ret .= "}\n";

        /*
        if (isset($_COOKIE['unitTest'])) {
            //$config->debug->benchmark = false;
        }
        */


        $ret .= "Vps_Setup::\$configSection = '".Vps_Setup::$configSection."';\n";
        $ret .= "if (isset(\$_SERVER['HTTP_HOST'])) {\n";
            $ret .= "    \$host = \$_SERVER['HTTP_HOST'];\n";
            $ret .= "    //www abschneiden damit www.test und www.preview usw auch funktionieren\n";
            $ret .= "    if (substr(\$host, 0, 4)== 'www.') \$host = substr(\$host, 4);\n";
            $ret .= "    if (substr(\$host, 0, 9)=='dev.test.') {\n";
            $ret .= "        Vps_Setup::\$configSection = 'devtest';\n";
            $ret .= "    } else if (substr(\$host, 0, 4)=='dev.') {\n";
            $ret .= "        Vps_Setup::\$configSection = 'dev';\n";
            $ret .= "    } else if (substr(\$host, 0, 5)=='test.' ||\n";
            $ret .= "            substr(\$host, 0, 3)=='qa.') {\n";
            $ret .= "        Vps_Setup::\$configSection = 'test';\n";
            $ret .= "    } else if (substr(\$host, 0, 8)=='preview.') {\n";
            $ret .= "        Vps_Setup::\$configSection = 'preview';\n";
            $ret .= "    }\n";
        $ret .= "}\n";


        // Falls redirectToDomain eingeschalten ist, umleiten
        if (Vps_Config::getValue('server.redirectToDomain')) {
            $ret .= "\$host = isset(\$_SERVER['HTTP_HOST']) ? \$_SERVER['HTTP_HOST'] : null;\n";
            $ret .= "if (\$host) {\n";
            $ret .= "    \$redirect = false;\n";
            if ($domains = Vps_Config::getValueArray('vpc.domains')) {
                $ret .= "    \$noRedirect = false;\n";
                foreach ($domains as $domain) {
                    $ret .= "    if ('{$domain['domain']}' == \$host) \$noRedirect = true;\n";
                }
                $ret .= "    if (!\$noRedirect) {\n";
                foreach ($domains as $domain) {
                    if (isset($domain['pattern'])) {
                        $ret .= "        if (preg_match('/{$domain['pattern']}/', \$host)) {\n";
                        if ($domain['noRedirectPattern']) {
                            $ret .= "            if (!preg_match('/{$domain['noRedirectPattern']}/', \$host)) {\n";
                            $ret .= "                \$redirect = '{$domain['domain']}';\n";
                            $ret .= "            }\n";
                            $ret .= "            break;\n";
                        }
                        $ret .= "        }\n";
                    } else {
                        $ret .= "        if (!\$redirect) \$redirect = '{$domain['domain']}';\n";
                    }
                }
                $ret .= "    }\n";
            } else if (Vps_Config::getValue('server.domain')) {
                $ret .= "    if (\$host != '".Vps_Config::getValue('server.domain')."') {\n";
                    if (Vps_Config::getValue('server.noRedirectPattern')) {
                        $ret .= "        if (!preg_match('/".Vps_Config::getValue('server.noRedirectPattern')."/', \$host)) {\n";
                        $ret .= "            \$redirect = '".Vps_Config::getValue('server.domain')."';\n";
                        $ret .= "        }\n";
                    } else {
                        $ret .= "        \$redirect = '".Vps_Config::getValue('server.domain')."';\n";
                    }
                $ret .= "    }\n";
            }
            $ret .= "    if (\$redirect) {\n";
            $ret .= "        \$target = Vps_Model_Abstract::getInstance('Vps_Util_Model_Redirects')\n";
            $ret .= "            ->findRedirectUrl('domainPath', array(\$host.\$_SERVER['REQUEST_URI'], 'http://'.\$host.\$_SERVER['REQUEST_URI']));\n";
            $ret .= "        if (!\$target) {\n";
            $ret .= "            \$target = Vps_Model_Abstract::getInstance('Vps_Util_Model_Redirects')\n";
            $ret .= "                ->findRedirectUrl('domain', \$host);\n";
            $ret .= "        }\n";
            $ret .= "        if (\$target) {\n";
            $ret .= "            //TODO: funktioniert nicht bei mehreren domains\n";
            $ret .= "            header('Location: http://'.\$redirect.\$target, true, 301);\n";
            $ret .= "        } else {\n";
            $ret .= "            header('Location: http://'.\$redirect.\$_SERVER['REQUEST_URI'], true, 301);\n";
            $ret .= "        }\n";
            $ret .= "        exit;\n";
            $ret .= "    }\n";
            $ret .= "}\n";
        }

        $ret .= "\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 14) == '/vps/util/apc/'\n";
        $ret .= ") {\n";
        $ret .= "    Vps_Util_Apc::dispatchUtils();\n";
        $ret .= "}\n";


        if (Vps_Config::getValue('showPlaceholder') && !Vps_Config::getValue('ignoreShowPlaceholder')) {
            $ret .= "if (php_sapi_name() != 'cli' && isset(\$_SERVER['REQUEST_URI']) && substr(\$_SERVER['REQUEST_URI'], 0, 8)!='/assets/') {\n";
            $ret .= "    $view = new Vps_View();\n";
            $ret .= "    echo $view->render('placeholder.tpl');\n";
            $ret .= "    exit;\n";
            $ret .= "    }\n";
        }


        if (Vps_Config::getValue('preLogin')) {
            $ret .= "if (php_sapi_name() != 'cli' && isset(\$_SERVER['REDIRECT_URL'])) {\n";
            $ret .= "    \$ignore = false;\n";
            foreach (Vps_Config::getValueArray('preLoginIgnore') as $i) {
                $ret .= "    if (substr(\$_SERVER['REDIRECT_URL'], 0, ".strlen($i).") == '$i') \$ignore = true;\n";
            }
            foreach (Vps_Config::getValueArray('preLoginIgnoreIp') as $i) {
                $ret .= "    if (\$_SERVER['REMOTE_ADDR'] == '$i') \$ignore = true;\n";
            }

            $ret .= "    if (!\$ignore && (empty(\$_SERVER['PHP_AUTH_USER'])\n";
            $ret .= "           || empty(\$_SERVER['PHP_AUTH_PW'])\n";
            $ret .= "            || \$_SERVER['PHP_AUTH_USER']!='".Vps_Config::getValue('preLoginUser')."'\n";
            $ret .= "           || \$_SERVER['PHP_AUTH_PW']!='".Vps_Config::getValue('preLoginPassword')."')\n";
            $ret .= "    ) {\n";
            $ret .= "        header('WWW-Authenticate: Basic realm=\"Testserver\"');\n";
            $ret .= "        throw new Vps_Exception_AccessDenied();\n";
            $ret .= "    }\n";
            $ret .= "}\n";
        }

        if ($tl = Vps_Config::getValue('debug.timeLimit')) {
            $ret .= "set_time_limit($tl);\n";
        }

        $ret .= "setlocale(LC_ALL, explode(', ', '".trlcVps('locale', 'C')."'));\n";
        $ret .= "setlocale(LC_NUMERIC, 'C');\n";

        $ret .= "Vps_Benchmark::checkpoint('setUp');\n";
        $ret .= "\n";

        return $ret;
    }
}
