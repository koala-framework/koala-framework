<?php
require_once('/www/public/markus/library/zend/1.10.7/Zend/Registry.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Registry.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Benchmark.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Loader.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Config.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Cache/Simple.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Debug.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Trl.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Data.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Data/Root.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Model/Select.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Select.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Abstract.php');
require_once('/wwwnas/public/markus/kwf/Kwc/Abstract.php');
require_once('/wwwnas/public/markus/kwf/Kwc/Paragraphs/Component.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Renderer/Abstract.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Renderer.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Cache.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/Cache/Mysql.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/View/Helper/Abstract.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/View/Renderer.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/View/Helper/Master.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/View/Helper/Component.php');
require_once('/wwwnas/public/markus/kwf/Kwf/Component/View/Helper/ComponentLink.php');
require_once('/wwwnas/public/markus/kwf/Kwf/View/Helper/ComponentLink.php');
Kwf_Benchmark::$startTime = microtime(true);

if (isset($_SERVER['HTTP_CLIENT_IP'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];

define('KWF_PATH', '/wwwnas/public/markus/kwf');
set_include_path('.:/usr/share/php:/usr/share/pear:/wwwnas/public/markus/kwf:/wwwnas/public/markus/kwf:/www/public/markus/library/zend/1.10.7:/www/public/markus/library/zend/1.10.7/wwwnas/public/markus/kwf:/www/public/markus/library/zend/1.10.7:cache/generated:/www/public/markus/library/tcpdf/5.9.023:/www/public/markus/library/phpexcel/1.7.6:/www/public/markus/library/pear/Contact_Vcard_Build/1.1.2:/www/public/markus/library/geshi/1.0.8.4:/www/public/markus/library/phpunit/3.4.0:/www/public/markus/library/sfYaml/rev33100:/wwwnas/public/markus/kwf/tests:tests');


Zend_Registry::setClassName('Kwf_Registry');

Kwf_Setup::$configClass = 'Kwf_Config_Web';


//here to be as fast as possible (and have no session)
if (isset($_SERVER['REQUEST_URI']) &&
    substr($_SERVER['REQUEST_URI'], 0, 25) == '/kwf/json-progress-status'
) {
    require_once('Kwf/Util/ProgressBar/DispatchStatus.php');
    Kwf_Util_ProgressBar_DispatchStatus::dispatch();
}

//here to have less dependencies
if (isset($_SERVER['REQUEST_URI']) &&
    substr($_SERVER['REQUEST_URI'], 0, 17) == '/kwf/check-config'
) {
    require_once('Kwf/Util/Check/Config.php');
    Kwf_Util_Check_Config::dispatch();
}
if (php_sapi_name() == 'cli' && isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'check-config') {
    require_once('Kwf/Util/Check/Config.php');
    Kwf_Util_Check_Config::dispatch();
}
Kwf_Benchmark::enableLog();
Kwf_Loader::registerAutoload();
ini_set('memory_limit', '128M');
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');
mb_internal_encoding('UTF-8');
iconv_set_encoding('internal_encoding', 'utf-8');
set_error_handler(array('Kwf_Debug', 'handleError'), E_ALL);
set_exception_handler(array('Kwf_Debug', 'handleException'));
umask(000); //nicht 002 weil wwwrun und kwcms in unterschiedlichen gruppen
Zend_Registry::set('requestNum', ''.floor(Kwf_Benchmark::$startTime*100));
if (isset($_POST['PHPSESSID'])) {
    //für swfupload
    Zend_Session::setId($_POST['PHPSESSID']);
}
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
if ($host) file_put_contents('cache/lastdomain', $host);
Kwf_Setup::$configSection = 'production';
if ($host) {
    //www abschneiden damit www.test und www.preview usw auch funktionieren
    $h = $host;
    if (substr($h, 0, 4)== 'www.') $h = substr($h, 4);
    if (substr($h, 0, 9)=='dev.test.') {
        Kwf_Setup::$configSection = 'devtest';
    } else if (substr($h, 0, 4)=='dev.') {
        Kwf_Setup::$configSection = 'dev';
    } else if (substr($h, 0, 5)=='test.' ||
            substr($h, 0, 3)=='qa.') {
        Kwf_Setup::$configSection = 'test';
    } else if (substr($h, 0, 8)=='preview.') {
        Kwf_Setup::$configSection = 'preview';
    }
}

if (isset($_SERVER['REQUEST_URI']) &&
    substr($_SERVER['REQUEST_URI'], 0, 14) == '/kwf/util/apc/'
) {
    Kwf_Util_Apc::dispatchUtils();
}
setlocale(LC_ALL, explode(', ', 'C'));
setlocale(LC_NUMERIC, 'C');
Kwf_Benchmark::checkpoint('setUp');

