<?php
class Kwf_Util_Setup
{
    public static function generateCode($configClass)
    {
        if (file_exists(KWF_PATH.'/include_path')) {
            $zendPath = trim(file_get_contents(KWF_PATH.'/include_path'));
            $zendPath = str_replace(
                '%version%',
                file_get_contents(KWF_PATH.'/include_path_version'),
                $zendPath);
        } else {
            die ('zend not found');
        }
        set_include_path(get_include_path(). PATH_SEPARATOR . $zendPath);

        $ip = KWF_PATH.PATH_SEPARATOR.$zendPath.PATH_SEPARATOR.getcwd();
        foreach (Kwf_Config::getValueArray('includepath') as $t=>$p) {
            $ip .= PATH_SEPARATOR . $p;
        }

        $ret = "<?php\n";

        $preloadClasses = array(
            'Zend_Registry',
            'Kwf_Registry',
            'Kwf_Benchmark',
            'Kwf_Loader',
            'Kwf_Config',
            'Kwf_Cache_Simple',
            'Kwf_Debug',
            'Kwf_Trl',
            'Kwf_Component_Data',
            'Kwf_Component_Data_Root',
            'Kwf_Model_Select',
            'Kwf_Component_Select',
            'Kwf_Component_Abstract',
            'Kwc_Abstract',
            'Kwc_Paragraphs_Component',
            'Kwf_Component_Renderer_Abstract',
            'Kwf_Component_Renderer',
            'Kwf_Component_Cache',
            'Kwf_Component_Cache_Mysql',
            'Kwf_Component_View_Helper_Abstract',
            'Kwf_Component_View_Renderer',
            'Kwf_Component_View_Helper_Master',
            'Kwf_Component_View_Helper_Component',
            'Kwf_Component_View_Helper_ComponentLink',
            'Kwf_View_Helper_ComponentLink',
        );
        foreach ($preloadClasses as $cls) {
            foreach (explode(PATH_SEPARATOR, $ip) as $path) {
                $file = $path.'/'.str_replace('_', '/', $cls).'.php';
                if (file_exists($file)) {
                    $ret .= "require_once('".$file."');\n";
                    break;
                }
            }
        }

        $ret .= "Kwf_Benchmark::\$startTime = microtime(true);\n";
        $ret .= "\n";
        $ret .= "if (isset(\$_SERVER['HTTP_CLIENT_IP'])) \$_SERVER['REMOTE_ADDR'] = \$_SERVER['HTTP_CLIENT_IP'];\n";
        $ret .= "\n";

        $ret .= "define('KWF_PATH', '".KWF_PATH."');\n";

        $ret .= "set_include_path('$ip');\n";
        $ret .= "\n";
        $ret .= "\n";
        $ret .= "//here to be as fast as possible (and have no session)\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 25) == '/kwf/json-progress-status'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_ProgressBar_DispatchStatus::dispatch();\n";
        $ret .= "}\n";
        $ret .= "\n";
        $ret .= "//here to have less dependencies\n";
        $ret .= "if (isset(\$_SERVER['REQUEST_URI']) &&\n";
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 17) == '/kwf/check-config'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";
        $ret .= "if (php_sapi_name() == 'cli' && isset(\$_SERVER['argv'][1]) && \$_SERVER['argv'][1] == 'check-config') {\n";
        $ret .= "    Kwf_Util_Check_Config::dispatch();\n";
        $ret .= "}\n";
        $ret .= "\n";
        $ret .= "Zend_Registry::setClassName('Kwf_Registry');\n";
        $ret .= "\n";
        $ret .= "Kwf_Setup::\$configClass = '$configClass';\n";
        $ret .= "\n";
        if (Kwf_Config::getValue('debug.componentCache.checkComponentModification')) {
            $ret .= "Kwf_Config::checkMasterFiles();\n";
        }

        if (Kwf_Config::getValue('debug.benchmark')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Kwf_Benchmark::enable();\n";
        }
        if (Kwf_Config::getValue('debug.benchmarkLog')) {
            //vor registerAutoload aufrufen damit wir dort benchmarken können
            $ret .= "Kwf_Benchmark::enableLog();\n";
        }

        $ret .= "Kwf_Loader::registerAutoload();\n";

        $ret .= "ini_set('memory_limit', '128M');\n";
        $ret .= "error_reporting(E_ALL);\n";
        $ret .= "date_default_timezone_set('Europe/Berlin');\n";
        $ret .= "mb_internal_encoding('UTF-8');\n";
        $ret .= "iconv_set_encoding('internal_encoding', 'utf-8');\n";
        $ret .= "set_error_handler(array('Kwf_Debug', 'handleError'), E_ALL);\n";
        $ret .= "set_exception_handler(array('Kwf_Debug', 'handleException'));\n";
        $ret .= "umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen\n";

        $ret .= "Zend_Registry::set('requestNum', ''.floor(Kwf_Benchmark::\$startTime*100));\n";

        if (Kwf_Config::getValue('debug.firephp') || Kwf_Config::getValue('debug.querylog')) {
            $ret .= "if (php_sapi_name() != 'cli') {\n";
            if (Kwf_Config::getValue('debug.firephp')) {
                $ret .= "    require_once 'FirePHPCore/FirePHP.class.php';\n";
                $ret .= "    FirePHP::init();\n";
            }

            if (Kwf_Config::getValue('debug.querylog')) {
                $ret .= "    header('X-Kwf-RequestNum: '.Zend_Registry::get('requestNum'));\n";
                $ret .= "    register_shutdown_function(array('Kwf_Setup', 'shutDown'));\n";
            }
            $ret .= "    ob_start();\n";
            $ret .= "}\n";
        }

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

        $ret .= "if (isset(\$_POST['PHPSESSID'])) {\n";
        $ret .= "    //für swfupload\n";
        $ret .= "    Zend_Session::setId(\$_POST['PHPSESSID']);\n";
        $ret .= "}\n";

        /*
        if (isset($_COOKIE['unitTest'])) {
            //$config->debug->benchmark = false;
        }
        */


        $ret .= "\$host = isset(\$_SERVER['HTTP_HOST']) ? \$_SERVER['HTTP_HOST'] : null;\n";


        $path = getcwd();
        if (file_exists('application/config_section')) {
            $configSection = trim(file_get_contents('config_section'));
        } else {
            $configSection = 'production';
        }

        $ret .= "Kwf_Setup::\$configSection = '".$configSection."';\n";
        $ret .= "if (\$host) {\n";
            $ret .= "    //www abschneiden damit www.test und www.preview usw auch funktionieren\n";
            $ret .= "    if (substr(\$host, 0, 4)== 'www.') \$host = substr(\$host, 4);\n";
            $ret .= "    if (substr(\$host, 0, 9)=='dev.test.') {\n";
            $ret .= "        Kwf_Setup::\$configSection = 'devtest';\n";
            $ret .= "    } else if (substr(\$host, 0, 4)=='dev.') {\n";
            $ret .= "        Kwf_Setup::\$configSection = 'dev';\n";
            $ret .= "    } else if (substr(\$host, 0, 5)=='test.' ||\n";
            $ret .= "            substr(\$host, 0, 3)=='qa.') {\n";
            $ret .= "        Kwf_Setup::\$configSection = 'test';\n";
            $ret .= "    } else if (substr(\$host, 0, 8)=='preview.') {\n";
            $ret .= "        Kwf_Setup::\$configSection = 'preview';\n";
            $ret .= "    }\n";
        $ret .= "}\n";

        // Falls redirectToDomain eingeschalten ist, umleiten
        if (Kwf_Config::getValue('server.redirectToDomain')) {
            $ret .= "if (\$host) {\n";
            $ret .= "    \$redirect = false;\n";
            if ($domains = Kwf_Config::getValueArray('kwc.domains')) {
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
                            //$ret .= "            break;\n";
                        }
                        $ret .= "        }\n";
                    } else {
                        $ret .= "        if (!\$redirect) \$redirect = '{$domain['domain']}';\n";
                    }
                }
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
            $ret .= "            ->findRedirectUrl('domainPath', array(\$host.\$_SERVER['REQUEST_URI'], 'http://'.\$host.\$_SERVER['REQUEST_URI']));\n";
            $ret .= "        if (!\$target) {\n";
            $ret .= "            \$target = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Redirects')\n";
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
        $ret .= "    substr(\$_SERVER['REQUEST_URI'], 0, 14) == '/kwf/util/apc/'\n";
        $ret .= ") {\n";
        $ret .= "    Kwf_Util_Apc::dispatchUtils();\n";
        $ret .= "}\n";


        if (Kwf_Config::getValue('showPlaceholder') && !Kwf_Config::getValue('ignoreShowPlaceholder')) {
            $ret .= "if (php_sapi_name() != 'cli' && isset(\$_SERVER['REQUEST_URI']) && substr(\$_SERVER['REQUEST_URI'], 0, 8)!='/assets/') {\n";
            $ret .= "    $view = new Kwf_View();\n";
            $ret .= "    echo $view->render('placeholder.tpl');\n";
            $ret .= "    exit;\n";
            $ret .= "    }\n";
        }


        if (Kwf_Config::getValue('preLogin')) {
            $ret .= "if (php_sapi_name() != 'cli' && isset(\$_SERVER['REDIRECT_URL'])) {\n";
            $ret .= "    \$ignore = false;\n";
            foreach (Kwf_Config::getValueArray('preLoginIgnore') as $i) {
                $ret .= "    if (substr(\$_SERVER['REDIRECT_URL'], 0, ".strlen($i).") == '$i') \$ignore = true;\n";
            }
            foreach (Kwf_Config::getValueArray('preLoginIgnoreIp') as $i) {
                $ret .= "    if (\$_SERVER['REMOTE_ADDR'] == '$i') \$ignore = true;\n";
            }

            $ret .= "    if (!\$ignore && (empty(\$_SERVER['PHP_AUTH_USER'])\n";
            $ret .= "           || empty(\$_SERVER['PHP_AUTH_PW'])\n";
            $ret .= "            || \$_SERVER['PHP_AUTH_USER']!='".Kwf_Config::getValue('preLoginUser')."'\n";
            $ret .= "           || \$_SERVER['PHP_AUTH_PW']!='".Kwf_Config::getValue('preLoginPassword')."')\n";
            $ret .= "    ) {\n";
            $ret .= "        header('WWW-Authenticate: Basic realm=\"Testserver\"');\n";
            $ret .= "        throw new Kwf_Exception_AccessDenied();\n";
            $ret .= "    }\n";
            $ret .= "}\n";
        }

        if ($tl = Kwf_Config::getValue('debug.timeLimit')) {
            $ret .= "set_time_limit($tl);\n";
        }

        $ret .= "setlocale(LC_ALL, explode(', ', '".trlcKwf('locale', 'C')."'));\n";
        $ret .= "setlocale(LC_NUMERIC, 'C');\n";

        $ret .= "Kwf_Benchmark::checkpoint('setUp');\n";
        $ret .= "\n";

        return $ret;
    }
}
